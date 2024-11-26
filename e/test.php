<?php
  require('db.php');
  $sql = 'SELECT * FROM audiocloudTracks';
  $res = mysqli_query($link, $sql);
  for($i=0; $i<mysqli_num_rows($res); ++$i){
    $row = mysqli_fetch_assoc($res);
    echo $row['audioFile'] . "<br>";
  }
?>