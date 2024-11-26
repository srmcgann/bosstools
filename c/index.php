<?php 
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
  if(isset($_GET['i'])) $query = explode('/', $_GET['i']);
  $title = 'words';
  $image = 'https://jsbot.twilightparadox.com/1pnBdc.png';
  if(isset($_GET['i']) && $query[1] === 'post'){
    $id = alphaToDec(mysqli_real_escape_string($link, $query[2]));
    $sql = 'SELECT * FROM words WHERE id = ' . $id;
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      $title = $row['author'] . ' - ' . $row['title'];
      $sql = 'SELECT name, avatar FROM users WHERE name LIKE "' . $row['author'] . '"';
      $res = mysqli_query($link, $sql);
      if(mysqli_num_rows($res)){
        $row = mysqli_fetch_assoc($res);
        if($row['avatar']) $image = $row['avatar'];
      }
    }
  } elseif(isset($_GET['i']) && $query[1] === 'u') {
    $sql = 'SELECT name, avatar FROM users WHERE name LIKE "' . mysqli_real_escape_string($link, $query[2]) . '";';
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      if($row['name']) $title = 'words - ' . $row['name'];
      if($row['avatar']) $image = $row['avatar'];
    }
  } else {
    $image = 'https://jsbot.twilightparadox.com/1GY3GM.png';
  }
  $url =  (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https:" : "https:") . "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
  $url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
  $type = 'website';
  $description = 'boss.mindhackers.org - a free blog';
?> <!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=1024"><title><?php echo $title?></title> <?php  if($image){?> <link rel="icon" href="<?php echo 'https://code.twilightparadox.com/a/imgProxy.php?url='.$image?>"><?php }else{?> <link rel="icon" href="https://jsbot.twilightparadox.com/1GY3GM.png"> <?php }?> <?php  if($image){?><meta property="og:url" content="<?php echo $url?>"><?php }?> <?php  if($image){?><meta property="og:type" content="<?php echo $type?>"><?php }?> <?php  if($image){?><meta property="og:title" content="<?php echo $title?>"><?php }?> <?php  if($image){?><meta property="og:description" content="<?php echo $description?>"><?php }?> <?php  if($image){?><meta property="og:image" content="<?php echo $image?>"><?php }?> <?php  if($image){?><meta property="og:image:secure_url" content="<?php echo 'https://code.twilightparadox.com/a/imgProxy.php?url='.$image?>"><?php }?> <link href="css/app.f2e0b410.css" rel="preload" as="style"><link href="js/app.1f30cbfa.js" rel="preload" as="script"><link href="js/chunk-vendors.f6efeced.js" rel="preload" as="script"><link href="css/app.f2e0b410.css" rel="stylesheet"></head><body><noscript><strong>We're sorry but words.whitehotrobot.com doesn't work properly without JavaScript enabled. Please enable it to continue.</strong></noscript><div id="app"></div><script src="js/chunk-vendors.f6efeced.js"></script><script src="js/app.1f30cbfa.js"></script></body></html>