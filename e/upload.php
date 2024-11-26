<?php 
require_once('db.php');
$targetDir = 'audio/';
$tmpFilePath = $_FILES['file']['tmp_name'];
$success = false;
file_put_contents('./audio/test.txt', json_encode($_FILES));
//die();
if ($tmpFilePath != ""){
  $filename = strtoupper($_FILES['file']['name']);
  $suffix = '.' . substr($filename, strlen($filename)-3);
  if($suffix == '.MP3' || $suffix == '.WAV' || $suffix == '.OGG'){
    $newFileName = hash_file('md5', $tmpFilePath) . $suffix;
    $newFilePath = $targetDir . $newFileName;
    rename($tmpFilePath, $newFilePath);
    chmod($newFilePath, 0755);
    $userID = mysqli_real_escape_string($link, $_POST['userID']);
    $author = mysqli_real_escape_string($link, $_POST['author']);
    $trackName = mysqli_real_escape_string($link, $_POST['trackName']);
    $description = '';
    $audioFile = $baseAssetsURL . '/' . $newFileName;
    $sql = 'INSERT INTO audiocloudTracks (userID, author, trackName, playlists, private, description, audioFile, plays) VALUES('.$userID.',"'.$author.'","'.$trackName.'","[]",1,"'.$description.'","'.$audioFile.'",0)';
    mysqli_query($link, $sql);
    $success = true;
  }
  echo json_encode([$success, $sql]);
}else{
  echo [false, 'doh!'];
}
?>