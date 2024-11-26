<?php

  $tgt = "jsbot.cantelope.org/uploads";
  $newval = "srmcgann.github.io/assets";

  echo "processing...";

  require('db.php');
  $sql = 'USE videodemos';
  mysqli_query($link, $sql);
  $sql = 'SHOW TABLES;';
  $res = mysqli_query($link, $sql);
  for($i = 0; $i<mysqli_num_rows($res); ++$i){
    $row = mysqli_fetch_assoc($res);
    //echo json_encode($row) . "\n";
    $tableName = $row['Tables_in_videodemos'];
    $sql = "SHOW COLUMNS FROM $tableName";
    $res2 = mysqli_query($link, $sql);
    for($j = 0; $j < mysqli_num_rows($res2); ++$j){
      $row2 = mysqli_fetch_assoc($res2);
      //echo json_encode($row2) . "\n";
      $column = $row2['Field'];
      $sql = "SELECT $column, id FROM $tableName";
      $res3 = mysqli_query($link, $sql);
      for($k = 0; $k < mysqli_num_rows($res3); ++$k){
        $row3 = mysqli_fetch_assoc($res3);
        $val = $row3[$column];
        $id = $row3['id'];
        if($id && strpos($val, $tgt)!==false){
          $updatedVal = mysqli_real_escape_string($link, str_replace($tgt, $newval, $val));
	  $sql = "UPDATE $tableName SET $column=\"$updatedVal\" WHERE id = $id";
          mysqli_query($link, $sql);
          echo ".";
          ob_flush();
          flush();
        }
      }
    }
  }
  echo "\n\n\ndone\n";
?>
