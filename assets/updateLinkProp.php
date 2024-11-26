<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(0);
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $userName = mysqli_real_escape_string($link, $data->{'userName'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $linkID = mysqli_real_escape_string($link, $data->{'linkID'});
  $prop = mysqli_real_escape_string($link, $data->{'prop'});
  $value = mysqli_real_escape_string($link, $data->{'value'});
  
  $success = false;
  if($userName && $passhash){
    $sql = "SELECT * FROM imjurUsers WHERE name LIKE \"$userName\" AND passhash LIKE BINARY \"$passhash\";";
    $res = mysqli_query($link, $sql);
  }
  if(mysqli_num_rows($res)){
    $row = mysqli_fetch_assoc($res);
    if($row['enabled'] || $row['admin']){
      $userID = $row['id'];
      $sql = "UPDATE imjurUploads SET `$prop` = \"$value\" WHERE id = $linkID";
      mysqli_query($link, $sql);
      echo json_encode([$success, 1, $sql]);
    } else {
      echo json_encode([$success, 1, $sql, $slugs]);
    }
  } else {
    echo json_encode([$success, 2, $sql, $slugs]);
  }
?>