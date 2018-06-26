<?php


class csvCreator{

    public $lists = array(), $tags = array();

    public $titles = array('url', 'title', 'h1', 'description');

    public $patterns = array(
        'title' => "#<title>(.+?)</title>#su",
        'h1'    => "#<h1>(.+?)</h1>#su",
        'description' => '/<meta(?=[^>]* name *= *"?description"?) [^>]*?(?<= )content *= *"([^"]*)"[^>]*>/i'
    );

    public $count;

    public $output = 'file.csv';

    public function getCsv(array &$array, $titles, $file){
        if(count($array) == 0){
            return null;
        }

        $df = fopen($file, 'w');
        fputcsv($df, $titles, ';');
        foreach ($array as $row) {
            fputcsv($df, $row, ';');
        }
        fclose($df);

        return ;
    }

    public function multiple_threads_request(array &$nodes){
        $count = count($this->lists);
        $mh = curl_multi_init();
        $curl_array = array();
        foreach ($nodes as $i => $url) {
            $curl_array[$i] = curl_init($url);
            curl_setopt($curl_array[$i], CURLOPT_RETURNTRANSFER, true);
            curl_multi_add_handle($mh, $curl_array[$i]);
        }
        $running = null;
        do {
            usleep(100);
            curl_multi_exec($mh, $running);
        } while ($running > 0);

        $res = array();
        foreach ($nodes as $i => $url) {
            $res[$url] = curl_multi_getcontent($curl_array[$i]);
            preg_match($this->patterns['title'], $res[$url], $this->tags[$count][]);
            preg_match($this->patterns['h1'], $res[$url], $this->tags[$count][]);
            preg_match($this->patterns['description'], $res[$url], $this->tags[$count][]);
            $this->lists[$count]['url'] = $url;
            $this->lists[$count]['title'] = iconv('utf-8', 'cp1251', $this->tags[$count][0][1]);
            $this->lists[$count]['h1'] = iconv('utf-8', 'cp1251', $this->tags[$count][1][1]);
            $this->lists[$count]['description'] = iconv("utf-8", "cp1251", $this->tags[$count][2][1]);
            $count++;
        }

        foreach ($nodes as $i => $url) {
            curl_multi_remove_handle($mh, $curl_array[$i]);
        }
        curl_multi_close($mh);

        return ;
    }

    public function chunkUrls(array &$urls){
        $packs = array_chunk($urls,25);
        foreach ($packs as $pack){
            $this->multiple_threads_request($pack);
        }

        $this->getCsv($this->lists,$this->titles,$this->output);

        return $this->lists;
    }
}