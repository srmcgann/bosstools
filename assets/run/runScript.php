<?php
  error_reporting(E_ALL);
  require_once('../db.php');
  $data = json_decode(file_get_contents('php://input'));
  $password = mysqli_real_escape_string($link, $data->{'pass'});
  $success = false;
  if(password_verify($password, '$2y$10$ZEln/HLFPu7RSHzuQ9offOT6VIqjiIDdWXBQ1AJ77pNAmkcQTtXne')){
    $source = $data->{'source'};
    if($source){
      file_put_contents('tempsource.php', $source);
      if(require('tempsource.php')){
        $success = true;
        echo "\n[no errors...]";
        unlink('tempsource.php');
      }
    }else{
      echo json_encode([$success]);
    }
  }else{
    die(json_encode([$success, 'no auth']));
  }
?>