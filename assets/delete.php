<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(0);
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $userName = mysqli_real_escape_string($link, $data->{'userName'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $slugs = $data->{'slugs'};
  
  $success = false;
  $delFileCount = 0;
  $delRecCount = 0;
  $anon = false;
  if($userName && $passhash){
    $sql = "SELECT * FROM imjurUsers WHERE name LIKE \"$userName\" AND passhash LIKE BINARY \"$passhash\";";
    $res = mysqli_query($link, $sql);
  }else{
    $anon = true;
  }
  if($anon || mysqli_num_rows($res)){
    if(!$anon) $row = mysqli_fetch_assoc($res);
    if($anon || $row['enabled'] || $row['admin']){
      $userID = $anon ? -1 : $row['id'];
      forEach($slugs as $slug){
        $slug = mysqli_real_escape_string($link, $slug);
        $sql = "SELECT * FROM imjurUploads WHERE slug LIKE BINARY \"$slug\" AND userID = $userID";
        $res2 = mysqli_query($link, $sql);
        if(mysqli_num_rows($res2)){
          $row2 = mysqli_fetch_assoc($res2);
          $originalSlug = $row2['originalSlug'];
          $uploadID = $row2['id'];
          $sql = "DELETE FROM imjurUploads WHERE id = $uploadID AND userID = $userID";
          mysqli_query($link, $sql);
          $sql = "SELECT * FROM imjurUploads WHERE originalSlug LIKE BINARY \"$originalSlug\"";
          $res2 = mysqli_query($link, $sql);
          if(mysqli_num_rows($res2) == 0 && $originalSlug && strlen($originalSlug) > 1 && $slug === $originalSlug){
            forEach(glob("resources/$originalSlug.*") as $file){
              unlink($file);
              $delFileCount++;
            }
          }
          $success = true;
          $sql = "DELETE FROM imjurVotes WHERE uploadID = $uploadID";
          mysqli_query($link, $sql);
          $sql = "DELETE FROM imjurComments WHERE uploadID = $uploadID";
          mysqli_query($link, $sql);
          
          $sql = "SELECT id, meta FROM imjurCollections";
          $res2 = mysqli_query($link, $sql);
          $cullSlug = $slug;
          for($i=0; $i<mysqli_num_rows($res); ++$i){
            $row = mysqli_fetch_assoc($res);
            if(isset($row['meta'])){
              $meta = json_decode($row['meta']);
              $colID = $row['id'];
              $ar = [];
              forEach($meta->{'slugs'} as $slug){
                if($slug != $cullSlug) $ar[] = $slug;
              }
              $meta->{'slugs'} = $ar;
              $meta = mysqli_real_escape_string($link, json_encode($meta));
              $sql = "UPDATE imjurCollections SET `meta` = \"$meta\" WHERE id = $colID";
              mysqli_query($link, $sql);
            }
          }
          $delRecCount++;
        }
      }
      echo json_encode([$success, 1, $sql, $delFileCount, $delRecCount, $slugs]);
    } else {
      echo json_encode([$success, 1, $sql, $slugs]);
    }
  } else {
    echo json_encode([$success, 2, $sql, $slugs]);
  }
?>