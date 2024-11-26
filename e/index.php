<?php 
  function alphaToDec($val){
    $pow=0;
    $res=0;
    while($val!=""){
      $cur=$val[strlen($val)-1];
      $val=substr($val,0,strlen($val)-1);
      $mul=ord($cur)<58?$cur:ord($cur)-(ord($cur)>96?87:29);
      $res+=floatval($mul)*pow(62,$pow);
      $pow++;
    }
    return floatval($res);
  }

  require('db.php');
  $query = explode('/',$_GET['i']);
  $title = 'audiocloud';
  $image = 'https://jsbot.twilightparadox.com/1pnBdc.png';
  if($query[0] === 'track'){
    $id = alphaToDec(mysqli_real_escape_string($link, $query[1]));
    $sql = 'SELECT * FROM audiocloudTracks WHERE id = ' . $id;
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      $title = $row['author'] . ' - ' . $row['trackName'];
      $sql = 'SELECT name, avatar FROM users WHERE name LIKE "' . $row['author'] . '"';
      $res = mysqli_query($link, $sql);
      if(mysqli_num_rows($res)){
        $row = mysqli_fetch_assoc($res);
        if($row['avatar']) $image = $row['avatar'];
      }
    }
  } elseif($query[0] === 'u') {
    $sql = 'SELECT name, avatar FROM users WHERE name LIKE "' . mysqli_real_escape_string($link, $query[1]) . '";';
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      if($row['name']) $title = 'audiocloud - ' . $row['name'];
      if($row['avatar']) $image = $row['avatar'];
    }
  } else {
    $image = 'https://jsbot.twilightparadox.com/1GY3GM.png';
  }
  $url =  (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https:" : "https:") . "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
  $url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
  $type = 'website';
  $description = 'audiocloud - a free platform for musicians';
?> <!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=800"><title><?php echo $title?></title><meta name="description" content="<?php echo $description?>"><meta name="keywords" content="<?php $title . ' ' . $description?>"> <?php  if($image){?> <link rel="icon" href="<?php echo 'https://code.whitehotrobot.com/imgProxy.php?url='.$image?>"><?php }else{?> <link rel="icon" href="https://jsbot.twilightparadox.com/1GY3GM.png"> <?php }?> <?php  if($image){?><meta property="og:url" content="<?php echo $url?>"><?php }?> <?php  if($image){?><meta property="og:type" content="<?php echo $type?>"><?php }?> <?php  if($image){?><meta property="og:title" content="<?php echo $title?>"><?php }?> <?php  if($image){?><meta property="og:description" content="<?php echo $description?>"><?php }?> <?php  if($image){?><meta property="og:image" content="<?php echo $image?>"><?php }?> <?php  if($image){?><meta property="og:image:secure_url" content="<?php echo 'https://code.whitehotrobot.com/imgProxy.php?url='.$image?>"><?php }?> <link href="css/app.f3b89fdb.css" rel="preload" as="style"><link href="js/app.ce42619a.js" rel="preload" as="script"><link href="js/chunk-vendors.9fbcd818.js" rel="preload" as="script"><link href="css/app.f3b89fdb.css" rel="stylesheet"></head><body><noscript><strong>We're sorry but audiocloud.whitehotrobot.com doesn't work properly without JavaScript enabled. Please enable it to continue.</strong></noscript><div id="app"></div><script src="js/chunk-vendors.9fbcd818.js"></script><script src="js/app.ce42619a.js"></script></body></html>