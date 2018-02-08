<?php
$name = 'ibr4himfidan'; // çekmek istediğiniz sayfa
$json = json_decode(file_get_contents('https://www.instagram.com/'.$name.'/?__a=1'));

$BaseURL = 'https://www.instagram.com/'.$name.'/?__a=1';

function getImages($link, $fullName){
  $info = pathinfo($link);
  $exte = strtolower($info['extension']);
  $name = $info['filename'];
  $file = ($name) ? $name.'.'. $exte : $info['basename'];
  $dire = 'images/'.$fullName.'/'.$file;
  if(file_exists($dire)){
    echo 'Klasör var <br />';
  } else {
    @$create = mkdir('images/'.$fullName, 0777);
  }
  $curl = curl_init($link);
  $fopen = fopen($dire, 'w');
  curl_setopt($curl, CURLOPT_HEADER, 0);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_0);
  curl_setopt($curl, CURLOPT_FILE, $fopen);
  curl_exec($curl);
  curl_close($curl);
  fclose($fopen);
}

$iterationUrl = $BaseURL;
$tryNext = true;
$limit = 10000;
$found = 0;
while ($tryNext) {
    $tryNext = false;
    $response = file_get_contents($iterationUrl);
    if ($response === false) {
        break;
    }
    $data = json_decode($response, true);
    if ($data === null) {
        break;
    }
    $media = $data['user']['media'];
    $found += count($media['nodes']);
    if ($media['page_info']['has_next_page'] && $found < $limit) {
        $iterationUrl = $BaseURL . '&max_id=' . $media['page_info']['end_cursor'];
        $tryNext = true;
    }
    foreach($media['nodes'] as $value){
      getImages($value['display_src'], $fullName = $name);
    }
}
?>
