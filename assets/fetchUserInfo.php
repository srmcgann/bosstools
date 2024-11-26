<?php
  require_once('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $userID = mysqli_real_escape_string($link, $data->{'userID'});
  $sql = "SELECT * FROM imjurUsers WHERE id = $userID";
  $res = mysqli_query($link, $sql);
  if(mysqli_num_rows($res)){
    $row = mysqli_fetch_assoc($res);
    echo json_encode([
      true, [
        'id' => $row['id'],
        'name' => $row['name'],
        'dateSeen' => $row['dateSeen'],
        'dateJoined' => $row['dateJoined'],
        'enabled' => $row['enabled'],
        'avatar' => $row['avatar'],
        'admin' => $row['admin']
        //$row['demoPostsPerPage']
      ]
    ]);
  } else {
    echo json_encode([false,'']);
  }
?>
