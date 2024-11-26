<?php
  error_reporting(0);
  function alphaToDec($val){
    $pow=0;
    $res=0;
    while($val!=""){
      $cur=$val[strlen($val)-1];
      $val=substr($val,0,strlen($val)-1);
      $mul=ord($cur)<58?$cur:ord($cur)-(ord($cur)>96?87:29);
      $res+=intval($mul)*pow(62,$pow);
      $pow++;
    }
    return intval($res);
  }

  require('db.php');
  $query = explode('/',$_GET['i']);
  $title = 'Videos & Demos';
  if($query[0] === 'd'){
    $id = alphaToDec(mysqli_real_escape_string($link, $query[1]));
    $sql = 'SELECT * FROM items WHERE id = ' . $id;
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      if($row['title']) $title = $row['title'];
      $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https:" : "http:") . "//{$_SERVER['HTTP_HOST']}/vidThumb.php?id=" . $id;
		  $image = json_decode(file_get_contents($url));
    }
  } elseif($query[0] === 'u') {
    $sql = 'SELECT name, avatar FROM users  WHERE name LIKE "' . mysqli_real_escape_string($link, $query[1]) . '";';
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      if($row['name']) $title = $row['name'];
      $image = $row['avatar'];
    }
  } else {
    $image = 'https://srmcgann.github.io/assets/1HVS37.png';
  }
  $url =  (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https:" : "http:") . "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
  $url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
  $type = 'website';
  $description = 'Free code playground with the option to feature a video.';
?> <!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=1024"><title><?=$title?></title><meta name="description" content="<?=$description?>"><meta name="keywords" content="<?$title . ' ' . $description?>"> <? if($image){?> <link rel="icon" href="<?='https://boss.mindhackers.org/a/imgProxy.php?url='.$image?>"><?}else{?> <link rel="icon" href="https://srmcgann.github.io/assets/1HVS37.png"> <?}?> <? if($image){?><meta property="og:url" content="<?=$url?>"><?}?> <? if($image){?><meta property="og:type" content="<?=$type?>"><?}?> <? if($image){?><meta property="og:title" content="<?=$title?>"><?}?> <? if($image){?><meta property="og:description" content="<?=$description?>"><?}?> <? if($image){?><meta property="og:image" content="<?=$image?>"><?}?> <? if($image){?><meta property="og:image:secure_url" content="<?='https://boss.mindhackers.org/a/imgProxy.php?url='.$image?>"><?}?> <link href="css/app.ac809fec.css" rel="preload" as="style"><link href="js/app.ef52b6f9.js" rel="preload" as="script"><link href="js/chunk-vendors.65a56b13.js" rel="preload" as="script"><link href="css/app.ac809fec.css" rel="stylesheet"></head><body><noscript><strong>We're sorry but vidlist.whitehotrobot.com doesn't work properly without JavaScript enabled. Please enable it to continue...</strong></noscript><div id="app"></div><script src="js/chunk-vendors.65a56b13.js"></script><script src="js/app.ef52b6f9.js"></script></body></html>
