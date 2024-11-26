<?php
  //ini_set('display_errors', 1);
  //ini_set('display_startup_errors', 1);
  error_reporting(0);
  require('../db.php');
  require('../functions.php');
  $asset = mysqli_real_escape_string($link, $_GET['asset']);
  if($asset){
    $slug = explode('.', $asset)[0];
    $suffix = explode('.', $asset)[1];
    $id = alphaToDec($slug);
    $sql = "SELECT * FROM imjurUploads WHERE id = $id";
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      $filetype = $row['filetype'];
      $sql = "UPDATE imjurUploads SET views = views + 1 WHERE id = $id";
      mysqli_query($link, $sql);
      $originalSlug = $row['originalSlug'];
      $src = fopen("../resources/$originalSlug.$suffix", 'r');
      stream_set_blocking($src, true);
      header("Content-Type: $filetype");
      do{
        $data = fread($src, 1024*128);
        if($data){
          echo $data;
          flush();
          //ob_flush();
        }
      }while($data);
    }
  }
?>