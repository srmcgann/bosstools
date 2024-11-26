<?php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(1);

  $res    = $_GET['res'];
  $size   = getimagesize($res);
  $width  = $size[0];
  $height = $size[1];
  $type   = $size['mime'];
  
  $tgtMax = 200;
  
  if($width > $height){
    $newWidth = $tgtMax;
    $rdx = $width/$newWidth;
    $newHeight = $height / $rdx;
  }else{
    $newHeight = $tgtMax;
    $rdx = $height/$newHeight;
    $newWidth = $width / $rdx;
  }
  $newWidth = floor($newWidth);
  $newHeight= floor($newHeight);
  
  switch($type){
    case 'image/jpeg':
      $src = imagecreatefromjpeg($res);
      break;
    case 'image/png':
      $src = imagecreatefrompng($res);
      break;
    case 'image/gif':
      $src = imagecreatefromgif($res);
      break;
    case 'image/webp':
      $src = imagecreatefromwebp($res);
      break;
  }
  
  $thumb = imagecreatetruecolor($newWidth, $newHeight);
  imagecopyresized($thumb, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
  
  header('Content-Type: image/jpeg');
  imagejpeg($thumb);
  $data = ob_get_contents();
?>
