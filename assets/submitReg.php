<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(0);
  require_once('db.php');
  require_once('functions.php');
  $baseURL = $_SERVER['REQUEST_URI'];

  function escapeUserName($userName, $id){
    return str_replace('\\', '', str_replace(';', '', str_replace("'", '', (str_replace(' ', '', str_replace("\t", '', str_replace(';', '', str_replace("\n", '', str_replace('&', '', str_replace('|', '', str_replace('@', '', str_replace('#', '', str_replace('$', '', str_replace('%', '', str_replace('^', '', str_replace('(', '', str_replace(')', '', str_replace('?','', str_replace('!', '', str_replace('_', '', str_replace('`', '', str_replace("'", '', str_replace( '~', '', str_replace('"', '', $userName))))))))))))))))))))))).decToAlpha($id));
  }


  $data = json_decode(file_get_contents('php://input'));
  $userName = mysqli_real_escape_string($link, $data->{'userName'});
  $userName = str_replace(';', '', $userName);
  $password = mysqli_real_escape_string($link, $data->{'password'});
  $avatar = mysqli_real_escape_string($link, $data->{'avatar'});

  $available = checkUserNameAvailability(urlencode($userName));
  if($available && $password){
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO imjurUsers (name, escaped_name, passhash, avatar) VALUES(\"$userName\", \"\", \"$hash\",\"$avatar\")";
    mysqli_query($link, $sql);
    $id = mysqli_insert_id($link);
    $escaped_name = escapeUserName($userName, $id);
    $sql = 'UPDATE imjurUsers SET escaped_name = "'.$escaped_name.'" WHERE id = ' . $id;
    mysqli_query($link, $sql);
    echo json_encode([true, $hash, mysqli_insert_id($link), $sql]);
  } else {
    echo json_encode([false]);
  }
?>