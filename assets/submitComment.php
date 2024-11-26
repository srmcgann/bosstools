<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $userName = mysqli_real_escape_string($link, $data->{'userName'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $uploadID = intval(mysqli_real_escape_string($link, $data->{'linkID'}));
  $comment = mysqli_real_escape_string($link, $data->{'comment'});
  
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
      $sql = "INSERT INTO imjurComments (userID, text, upvotes, votesCast, uploadID) VALUES($userID, \"$comment\", 0, 0, $uploadID)";
      if(mysqli_query($link, $sql)){
        $comments = [];
        $sql = "SELECT * FROM imjurComments WHERE uploadID = $uploadID";
        $res = mysqli_query($link, $sql);
        for($i=0; $i<mysqli_num_rows($res); ++$i){
          $row = mysqli_fetch_assoc($res);
          $comments[] = $row;
        }
        $success = true;
        echo json_encode([$success, $comments]);
        die();
      }
    }
  }
  echo json_encode([$success]);
?>