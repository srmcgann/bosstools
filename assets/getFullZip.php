<?php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  require_once('db.php');
  require_once('functions.php');
  //$data = json_decode(file_get_contents('php://input'));
  //$userName = mysqli_real_escape_string($link, $data->{'userName'});
  //$passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $userName = mysqli_real_escape_string($link, $_GET['userName']);
  $passhash = mysqli_real_escape_string($link, $_GET['passhash']);
  $success = false;
  $resourceDir = './resources';
  if($userName && $passhash){
    $sql = "SELECT * FROM imjurUsers WHERE name LIKE \"$userName\" AND passhash LIKE BINARY \"$passhash\";";
    $res = mysqli_query($link, $sql);
  }
  if(mysqli_num_rows($res)){
    $row = mysqli_fetch_assoc($res);
    $userID = $row['id'];
    $sql = "SELECT * FROM imjurUploads WHERE userID = $userID";
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $f = tempnam(sys_get_temp_dir(), 'zip');
      register_shutdown_function('unlink', $f);
      $zip = new ZipArchive();
      $zip->open($f, ZipArchive::OVERWRITE);
      for($i=0; $i<mysqli_num_rows($res); ++$i){
        $row = mysqli_fetch_assoc($res);
        $filetype = $row['filetype'];
        $oslug    = $row['originalSlug'];
        $name     = $row['name'];
        $suffix   = getSuffix($filetype);
        $asset    = "$resourceDir/$oslug.$suffix";
        $fileName = "$name.$suffix";
        $zip->addFile($asset, $fileName);
      }
      $zip->close();
      $date = date("Y_M_d H_i_s");
      $zipfileName = "asset catalogue [$userName - $date].zip";
      header('Content-Type: application/zip');
      header('Content-Length: ' . filesize($f));
      header('Content-Disposition: attachment; filename="'.$zipfileName.'"');
      readfile($f);
    }
  }
?>