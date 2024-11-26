<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(0);

  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $userID = mysqli_real_escape_string($link, $data->{'userID'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $property = mysqli_real_escape_string($link, $data->{'property'});
  $value = mysqli_real_escape_string($link, $data->{'value'});
  $sql = "SELECT * FROM imjurUsers WHERE id = $userID AND passhash LIKE BINARY \"$passhash\"";
  $res = mysqli_query($link, $sql);
  $success = false;
  
  if($property !== 'pageSel' &&
     $property !== 'commentSel'
  ) die(json_encode(['false']));
  
  if(mysqli_num_rows($res)){
    $row = mysqli_fetch_assoc($res);
    if(!!$row['enabled']){
      $sql = "UPDATE imjurUsers SET `$property` = \"$value\" WHERE id = $userID";
      mysqli_query($link, $sql);
      $success = true;
    }
  }
  echo json_encode([$success, $sql]);
?>
