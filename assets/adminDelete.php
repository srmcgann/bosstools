<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

  require_once('db.php');
  require_once('functions.php');
  $data = json_decode(file_get_contents('php://input'));
  $userID = mysqli_real_escape_string($link, $data->{'userID'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $slug = mysqli_real_escape_string($link, $data->{'slug'});

  $resourceDir = "resources";

  $success = false;
  if($userID && $passhash){
    $sql = "SELECT * FROM imjurUsers WHERE id = $userID AND passhash LIKE BINARY \"$passhash\" AND admin = 1";
    $res = mysqli_query($link, $sql);
  }
  if(mysqli_num_rows($res)){
    
    $sql = "SELECT * FROM imjurUploads WHERE originalSlug LIKE BINARY \"$slug\"";
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      $hash = $row['hash'];
      $filetype = $row['filetype'];
      $sql = "SELECT * FROM imjurUploads WHERE hash LIKE BINARY \"$hash\"";
      $res = mysqli_query($link, $sql);
      $filename = "$resourceDir/$slug." . getSuffix($filetype);
      
      unlink($filename);
      for($i=0; $i<mysqli_num_rows($res); ++$i){
        $row = mysqli_fetch_assoc($res);
        $uid = $row['id'];
        $sql = "DELETE FROM imjurComments WHERE uploadID = $uid";
        mysqli_query($link, $sql);
        $sql = "DELETE FROM imjurVotes WHERE uploadID = $uid";
        mysqli_query($link, $sql);
        $sql = "DELETE FROM imjurUploads WHERE id = $uid";
        mysqli_query($link, $sql);
        $sql = "SELECT * FROM imjurCollections";
        $res2 = mysqli_query($link, $sql);
        for($j = 0; $j<mysqli_num_rows($res2); ++$j){
          $row2 = mysqli_fetch_assoc($res2);
          $meta = $row2['meta'];
          $cid = $row2['id'];
          $meta = json_decode($meta);
          $slugs = $meta->{'slugs'};
          $newSlugs = [];
          forEach($slugs as $slug_){
            if($slug != $slug_) $newSlugs[] = $slug_;
          }
          $meta->{'slugs'} = $newSlugs;
          $meta = mysqli_real_escape_string($link, json_encode($meta));
          $sql = "UPDATE imjurCollections SET meta = \"$meta\" WHERE id = $cid";
          mysqli_query($link, $sql);
        }

        $sql = "SELECT * FROM imjurFeaturedItems";
        $res2 = mysqli_query($link, $sql);
        for($j = 0; $j<mysqli_num_rows($res2); ++$j){
          $row2 = mysqli_fetch_assoc($res2);
          $meta = $row2['meta'];
          $meta = json_decode($meta);
          $newItems = [];
          forEach($meta as $item){
            $slug_ = $item->{'slug'};
            if($slug != $slug_){
              $newItems[] = $item;
            }
          }
          $meta = $newItems;
          $meta = mysqli_real_escape_string($link, json_encode($meta));
          $sql = "UPDATE imjurFeaturedItems SET meta = \"$meta\" WHERE id = $cid";
          mysqli_query($link, $sql);
        }
      }
      
      $success = true;
    }
  }
  echo json_encode([$success, $sql]);
?>