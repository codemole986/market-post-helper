<?php

$url = 'http://stockcharts.com/c-sc/sc?s={{TIKCER}}&p=D&b=5&g=0&i=0&r=1485176794965';
$ticker = isset($_REQUEST['ticker'])?'':$_REQUEST['ticker'];

$url = str_replace(['{{TIKCER}}'], $ticker, $url);

$img_cont = file_get_contents($url);

header("Content-type: image/jpeg");
print $img_cont;	