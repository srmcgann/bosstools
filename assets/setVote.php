<?php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  require_once('db.php');
  require_once('functions.php');
  $data      = json_decode(file_get_contents('php://input'));
  $userName  = mysqli_real_escape_string($link, $data->{'userName'});
  $passhash  = mysqli_real_escape_string($link, $data->{'passhash'});
  $slug      = mysqli_real_escape_string($link, $data->{'slug'});
  $value     = intval(mysqli_real_escape_string($link, $data->{'val'}));
  $total     = 0;
  $ct        = 0;
  $success   = false;
  if($userName && $passhash){
    $sql = "SELECT * FROM imjurUsers WHERE name LIKE \"$userName\" AND passhash LIKE BINARY \"$passhash\";";
    $res = mysqli_query($link, $sql);
  }
  if(mysqli_num_rows($res)){
    $row = mysqli_fetch_assoc($res);
    $userID = $row['id'];
    $uploadID = alphaToDec($slug);
    if($value){
      $sql = "SELECT * FROM imjurVotes WHERE userID = $userID AND uploadID = $uploadID";
      $res2 = mysqli_query($link, $sql);
      if(mysqli_num_rows($res2)){
        $row2 = mysqli_fetch_assoc($res2);
        $voteID = $row2['id'];
        $sql = "UPDATE imjurVotes SET value = $value WHERE id = $voteID";
      }else{
        $sql = "INSERT INTO imjurVotes (userID, uploadID, value) VALUES($userID, $uploadID, $value)";
      }
    }else{
      $sql = "DELETE FROM imjurVotes WHERE userID = $userID AND uploadID = $uploadID";
    }
    mysqli_query($link, $sql);
    
    $sql = "SELECT * FROM imjurVotes WHERE uploadID = \"$uploadID\"";
    $res = mysqli_query($link, $sql);
    for($i=0; $i<mysqli_num_rows($res); ++$i){
      $row = mysqli_fetch_assoc($res);
      $value = $row['value'];
      $total += $value;
      $ct++;
    }
    $sql = "UPDATE imjurUploads SET upvotes = $total, votesCast = $ct WHERE id = $uploadID";
    mysqli_query($link, $sql);
    $success = true;
  }
  echo json_encode([$success, $total, $ct]);

?>