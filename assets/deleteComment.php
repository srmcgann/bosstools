<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(0);

  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $userName = mysqli_real_escape_string($link, $data->{'userName'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $commentID = mysqli_real_escape_string($link, $data->{'commentID'});
  
  $success = false;
  $sql = "SELECT * FROM imjurUsers WHERE name LIKE \"$userName\" AND passhash LIKE BINARY \"$passhash\";";
  $res = mysqli_query($link, $sql);
  if(mysqli_num_rows($res)){
    $row = mysqli_fetch_assoc($res);
    
    // to delete a comment user must be an admin, comment-author, or asset-owner
    
    if($row['enabled'] || $row['admin']){
      $continue = false;
      $userID = $row['id'];
      if($row['admin']){
        $continue = true;
      }else{
        $sql = "SELECT * FROM imjurComments WHERE id = $commentID";
        $res2 = mysqli_query($link, $sql);
        $row2 = mysqli_fetch_assoc($res2);
        if($row2['userID'] == $userID){
          $continue = true;
        }else{
          $linkID = $row2['uploadID'];
          $sql = "SELECT * FROM imjurUploads WHERE id = $uploadID";
          $res3 = mysqli_query($link, $sql);
          $row3 = mysqli_fetch_assoc($res3);
          if($row3['userID'] == $userID) $continue = true;
        }
      }
      if($continue){
        $sql = "DELETE FROM imjurComments WHERE id = $commentID";
        mysqli_query($link, $sql);
        $success = true;
      }
    }
  }
  echo json_encode([$success, $sql]);
