<?php
error_reporting(0);
  require_once('db.php');
  if(!$_GET['userName']){
    $data = json_decode(file_get_contents('php://input'));
    $userName = mysqli_real_escape_string($link, $data->{'userName'});
  }else{
    $userName = mysqli_real_escape_string($link, $_GET['userName']);
  }
  $sql='SELECT * FROM imjurUsers WHERE name LIKE "'.$userName.'"';
  $res = mysqli_query($link, $sql);
  echo json_encode(mysqli_num_rows($res) === 0);
?>
