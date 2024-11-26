<?php
  //ini_set('display_errors', 1);
  //ini_set('display_startup_errors', 1);
  error_reporting(0);
  require('db.php');
  $data = json_decode(file_get_contents('php://input'));
  $userName = mysqli_real_escape_string($link, $data->{'userName'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $slug = mysqli_real_escape_string($link, $data->{'slug'});

  $success = false;
  $mode = false;
  if($userName && $passhash){
    $sql = "SELECT * FROM imjurUsers WHERE name LIKE \"$userName\" AND passhash LIKE BINARY \"$passhash\";";
    $res = mysqli_query($link, $sql);
  }
  if(mysqli_num_rows($res)){
    $row = mysqli_fetch_assoc($res);
    $userID = $row['id'];
    $sql = "SELECT * FROM imjurFeaturedItems";
    $res2 = mysqli_query($link, $sql);
    $curItems = [];
    for($i=0; $i<mysqli_num_rows($res2); ++$i){
      $row2 = mysqli_fetch_assoc($res2);
      $oldMeta = json_decode($row2['meta']);
      forEach($oldMeta as $metaItem){
        $curItems[] = $metaItem;
      }
    }
    $newMeta = [];
    forEach($curItems as $item){
      if($item -> {'slug'} == $slug){
        $mode = true;
      }else{
        $newMeta[] = $item;
      }
    }
    if(!$mode){
      $newMeta[] = ["slug"    => $slug,
                    "admin"   => $userID,
                    "updated" => date("Y-m-d H:i:s", strtotime("now")),
                    ];
    }
    $sql = "DELETE FROM imjurFeaturedItems";
    mysqli_query($link, $sql);
    $meta = $newMeta;
    $meta = mysqli_real_escape_string($link, json_encode($meta));
    $sql = "INSERT INTO imjurFeaturedItems (meta) VALUES(\"$meta\")";
    mysqli_query($link, $sql);
    $success = true;
  }
  echo json_encode([$success, $mode]);
?>