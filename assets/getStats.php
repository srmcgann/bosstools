<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(0);
  require_once('db.php');
  require_once('functions.php');
  
  $data = json_decode(file_get_contents('php://input'));
  $userID = '';
  $preval = $data->{'userID'};
  $passhash = '';
  if($preval !== null && $preval){
    $userID = mysqli_real_escape_string($link, $preval);
    $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  }
  
  $anon = false;
  $success = false;
  $resourceDir = "resources";

  // comment this line to make user stats globally private
  $anon = true;

  $enabled = 0;
  if($passhash){
    $sql = "SELECT * FROM imjurUsers WHERE id = $userID AND passhash LIKE BINARY\"$passhash\"";
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      $admin = $row['admin'];
      $enabled = $row['enabled'];
    }else{
      $anon = true;
    }
  }else{
    $anon = true;
  }

  //if(!$anon && !$enabled) $userID = false;

  if($userID){

    $success = true;
    $sql = "SELECT * FROM imjurUploads WHERE userID = $userID AND ($enabled OR NOT private)";
    $res = mysqli_query($link, $sql);
    $ret = [];
    $assets = [];
    for($i=0; $i<mysqli_num_rows($res); ++$i){
      $row = mysqli_fetch_assoc($res);
      $originalSlug = $row['originalSlug'];
      $filetype     = $row['filetype'];
      $resFile      = "$resourceDir/$originalSlug.".getSuffix($filetype);
      $obj = [
        'id'              => $row['id'],
        'slug'            => $row['slug'],
        'views'           => $row['views'],
        'size'            => filesize($resFile),
        'originalSlug'    => $originalSlug,
        'date'            => $row['date'],
        'hash'            => $row['hash'],
        'href'            => $resFile,
        'upvotes'         => $row['upvotes'],
        'votesCast'       => $row['votesCast'],
        'filetype'        => $filetype,
        'private'         => $row['private'],
        'meta'            => $row['meta'],
      ];
      $assets[] = $obj;
    }
    echo json_encode([$success, $assets]);
    
  }else{
    echo json_encode([$success]);
  }
?>