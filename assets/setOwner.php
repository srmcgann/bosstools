<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(0);

  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $userName = mysqli_real_escape_string($link, $data->{'userName'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $ids = $data->{'ids'};
  
  $success = false;
  $sql = "SELECT * FROM imjurUsers WHERE name LIKE \"$userName\" AND passhash LIKE BINARY \"$passhash\";";
  $res = mysqli_query($link, $sql);
  if(mysqli_num_rows($res)){
    $row = mysqli_fetch_assoc($res);
    if($row['enabled'] || $row['admin']){
      $userID = $row['id'];
      forEach($ids as $id){
        $id = intval(mysqli_real_escape_string($link, $id));
        $sql = "UPDATE imjurUploads SET userID = $userID WHERE id = $id";
        mysqli_query($link, $sql);
        $success = true;
      }
    }
  }
  echo json_encode([$success, $sql]);
