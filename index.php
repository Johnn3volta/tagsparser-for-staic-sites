<?php

require_once 'scandir.php';
require_once 'csvCreator.php';
require_once 'sitemap.php';
set_time_limit(0);
$file_ext = array('html');
$search = '/home/users/j/jump/domains';
$rpls = 'http:/';

$dir = dirname(__DIR__);

$urls = scanDir::scan($dir,$file_ext,true,$search,$rpls);

echo '<pre>';
$dd = new csvCreator();
print_r($dd->chunkUrls($urls));

$sitemap = new sitemap();

$map = $sitemap->generate_sitemap($urls);


$file = 'sitemap.xml';
$pf = fopen($file, "w");
fwrite($pf, $map);
fclose($pf);



