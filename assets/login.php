<?php
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $userName = mysqli_real_escape_string($link, $data->{'userName'});
  $password = mysqli_real_escape_string($link, $data->{'password'});
  $sql = "SELECT * FROM imjurUsers WHERE name LIKE \"$userName\";";
  $res = mysqli_query($link, $sql);
  if(mysqli_num_rows($res)){
    $row = mysqli_fetch_assoc($res);
    if($row['enabled'] && password_verify($password, $row['passhash'])){
      echo json_encode([true, $row['passhash'], $row['id'], $row['avatar'], $row['admin'], $row['pageSel'], $row['commentSel']]);
      $sql = "UPDATE imjurUsers SET dateSeen = NOW() WHERE id = {$row['id']}";
      mysqli_query($link, $sql);
    } else {
      echo json_encode([false, '', '']);
    }
  } else {
    echo json_encode([false, '', '']);
  }
?>