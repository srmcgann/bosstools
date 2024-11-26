<?php
  $title = "an online free digital asset hosting service";
  $args = explode('/', $_SERVER['REQUEST_URI']);
  if(sizeof($args) > 1 && $args[1] == 'assets') array_shift($args);
  require('db.php');
  require('functions.php');
  if(sizeof($args) > 1 && ($args[1] == 'col')){
    $collectionID = alphaToDec(mysqli_real_escape_string($link, $args[2]));
    $sql = "SELECT * FROM imjurCollections WHERE id = $collectionID";
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row          = mysqli_fetch_assoc($res);
      $meta         = json_decode($row['meta']);
      $name         = $row['name'];
      $slug         = $row['slug'];
      $views        = $meta->{'views'};
      $description  = $meta->{'description'};
      $items        = sizeof($meta->{'slugs'});
      $created      = date('F d Y', strtotime($meta->{'date'}));
      if($description) $description .= ' -';
      $title = "$name - $description $views views - [$items items] - created $created";
    }
  }
  if(sizeof($args) > 1 && ($args[1] == 'item')){
    $itemID = alphaToDec(mysqli_real_escape_string($link, $args[2]));
    $sql = "SELECT * FROM imjurUploads WHERE id = $itemID";
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row          = mysqli_fetch_assoc($res);
      $name         = $row['name'];
      $slug         = $row['slug'];
      $views        = $row['views'];
      $description  = $row['description'];
      $created      = date('F d Y', strtotime($row['date']));
      if($description) $description .= ' -';
      $title = "$name - $description $views views - created $created";
    }
  }
?> <!doctype html><html lang=""><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=800,initial-scale=1.5"><title><?=$title?></title><link rel="icon" href="/assets/favicon.png"><script defer="defer" src="/assets/js/chunk-vendors.54b3438d.js"></script><script defer="defer" src="/assets/js/app.f9089db7.js"></script><link href="/assets/css/app.d62dcc6c.css" rel="stylesheet"></head><body><noscript><strong>We're sorry but assets.free.nf doesn't work properly without JavaScript enabled. Please enable it to continue.</strong></noscript><div id="app"></div></body></html>
