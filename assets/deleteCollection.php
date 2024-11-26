<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(0);
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $userName = mysqli_real_escape_string($link, $data->{'userName'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $collectionID = mysqli_real_escape_string($link, $data->{'collectionID'});
  
  $success = false;
  if($userName && $passhash){
    $sql = "SELECT * FROM imjurUsers WHERE name LIKE \"$userName\" AND passhash LIKE BINARY \"$passhash\";";
    $res = mysqli_query($link, $sql);
  }
  if(mysqli_num_rows($res)){
    $row = mysqli_fetch_assoc($res);
    $userID = $row['id'];
    $enabled = $row['enabled'];
    $admin = $row['admin'];
    if($enabled || $admin){
      $sql = "SELECT * FROM imjurCollections WHERE id = $collectionID AND userID = $userID";
      $res = mysqli_query($link, $sql);
      if(mysqli_num_rows($res)){
        $sql = "DELETE FROM imjurCollections WHERE id = $collectionID";
        mysqli_query($link, $sql);
        $success = true;
      }
    }
  }
  echo json_encode([$success]);
?>