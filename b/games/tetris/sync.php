<?php
  require('db.php');

  $sql = "SELECT unix_timestamp()";
  $res = mysqli_query($link, $sql);
  $row = mysqli_fetch_assoc($res);
  $time = $row['unix_timestamp()'];
  
  $data = json_decode(file_get_contents('php://input'));
  $gameID = mysqli_real_escape_string($link, $data->{'gameID'});
  $playerNumber = mysqli_real_escape_string($link, $data->{'playerNumber'});
  $playerName = mysqli_real_escape_string($link, $data->{'playerName'});
  $receiveddata = $data->{'gamedata'};
  $sql = 'SELECT * FROM tetrisGames WHERE id = ' . $gameID;
  $res = mysqli_query($link, $sql);
  $row = mysqli_fetch_assoc($res);
  $gamefull = false;
  if($playerNumber == ''){
    if($row['gamedataA'] == '') {
      $playerNumber = 'A';
    } elseif($row['gamedataB'] == ''){
      $playerNumber = 'B';
    } elseif($row['gamedataC'] == ''){
      $playerNumber = 'C';
    } elseif($row['gamedataD'] == ''){
      $playerNumber = 'D';
    }
  }
  $receiveddata->{'lastUpdate'} = $time;
  $postdata = mysqli_real_escape_string($link, json_encode($receiveddata));
  switch($playerNumber){
    case 'A':
      $sql = 'UPDATE tetrisGames SET gamedataA = "' . $postdata . '" WHERE id = ' . $gameID;
    break;
    case 'B':
      $sql = 'UPDATE tetrisGames SET gamedataB = "' . $postdata . '" WHERE id = ' . $gameID;
    break;
    case 'C':
      $sql = 'UPDATE tetrisGames SET gamedataC = "' . $postdata . '" WHERE id = ' . $gameID;
    break;
    case 'D':
      $sql = 'UPDATE tetrisGames SET gamedataD = "' . $postdata . '" WHERE id = ' . $gameID;
    break;
    default:
      $gamefull = true;
    break;
  }
  if($gamefull){
    echo json_encode(['error - game full']);
  } else {
    if($sql && !($playerNumber == 'A' && $playerName =='')) {
      mysqli_query($link, $sql);
    }

    $sql = 'SELECT * FROM tetrisGames WHERE id = ' . $gameID;
    $res = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($res);

    for($i=0; $i<4; ++$i){
      switch($i){
        case 0: $col = 'gamedataA'; break;
        case 1: $col = 'gamedataB'; break;
        case 2: $col = 'gamedataC'; break;
        case 3: $col = 'gamedataD'; break;
      }
      if($row[$col]){
        $gamedata = json_decode($row[$col]);
        if(key_exists('lastUpdate', $gamedata)){
          if($time - $gamedata->{'lastUpdate'} > 10){
            $sql = 'UPDATE tetrisGames SET ' . $col . ' = "" WHERE id = ' . $gameID;
            mysqli_query($link, $sql);
          }
        }
      }
    }

    $sql = 'SELECT * FROM tetrisGames WHERE id = ' . $gameID;
    $res = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($res);

    echo json_encode([$playerNumber, $gameID, $row, $playerName, $receiveddata]);
  }
?>

