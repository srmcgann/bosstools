<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(0);

  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $userName = mysqli_real_escape_string($link, $data->{'userName'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $property = mysqli_real_escape_string($link, $data->{'property'});
  $linkID = mysqli_real_escape_string($link, $data->{'linkID'});
  $value = mysqli_real_escape_string($link, $data->{'value'});
  $sql = 'SELECT * FROM imjurUsers WHERE name LIKE "'.$userName.'" AND passhash = "'.$passhash.'"';
  $res = mysqli_query($link, $sql);
  $success = false;
  
  if($property !== 'private' &&
     $property !== 'name' &&
     $property !== 'description') die(['false']);
  
  if(mysqli_num_rows($res)){
    $row = mysqli_fetch_assoc($res);
    $userID = $row['id'];
    if($row['enabled']){
      $sql = "SELECT * FROM imjurUploads WHERE id = $linkID AND userID = $userID";
      $res = mysqli_query($link, $sql);
      if(mysqli_num_rows($res)){
        $success = true;
        $sql = "UPDATE imjurUploads SET `$property` = \"$value\" WHERE id = $linkID";
        mysqli_query($link, $sql);
      }
    }
  }
  echo json_encode([$success,$sql]);
?>
