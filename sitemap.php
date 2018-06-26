<?php


class sitemap{

    public function generate_sitemap(array &$urls){
        $sitemap = new SimpleXMLElement('<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"></urlset>');
        foreach ($urls as $url) {
            $ur = parse_url($url);
            if($url == "http://specodezhdatorg.ru/index.html"){
                $url_tag = $sitemap->addChild("url");
                $url_tag->addChild("loc", 'http://specodezhdatorg.ru/');
                $url_tag->addChild("lastmod", $this->getLastModifiedDate('Today'));
                $url_tag->addChild("changefreq", "weekly");
                $url_tag->addChild("priority", "1");
            }elseif(substr_count($ur['path'], '/') >= 2){
                $url_tag = $sitemap->addChild("url");
                $url_tag->addChild("loc", htmlspecialchars($url));
                $url_tag->addChild("lastmod", $this->getLastModifiedDate('Today'));
                $url_tag->addChild("changefreq", "weekly");
                $url_tag->addChild("priority", "0.80");
            }else{
                $url_tag = $sitemap->addChild("url");
                $url_tag->addChild("loc", htmlspecialchars($url));
                $url_tag->addChild("lastmod", $this->getLastModifiedDate('Today'));
                $url_tag->addChild("changefreq", "weekly");
                $url_tag->addChild("priority", "0.90");
            }

        }

        return $sitemap->asXML();

    }

    private function getLastModifiedDate($date){
        if(ctype_digit($date)){
            return date('Y-m-d', $date);
        }else{
            $date = strtotime($date);

            return date('Y-m-d', $date);
        }
    }
}