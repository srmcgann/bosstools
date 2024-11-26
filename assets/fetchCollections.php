<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(0);

  require_once('db.php');
  require_once('functions.php');
  $data = json_decode(file_get_contents('php://input'));
  $userID = mysqli_real_escape_string($link, $data->{'userID'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});

  $page = mysqli_real_escape_string($link, $data->{'page'});
  $overrideMaxResults = 1e3; //mysqli_real_escape_string($link, $data->{'maxResultsPerPage'});
  if($overrideMaxResults) $maxResultsPerPage = $overrideMaxResults;
  $start = $maxResultsPerPage * $page;

  if($passhash){
    $sql = "SELECT * FROM imjurUsers WHERE id = $userID AND passhash LIKE BINARY\"$passhash\"";
    $res = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($res);
    $admin = $row['admin'];
    $enabled = $row['enabled'];
  }

  $sql="SELECT id FROM imjurCollections WHERE userID = $userID";
  $res = mysqli_query($link, $sql);
  $totalRecords = mysqli_num_rows($res);
  $totalPages = floor(($totalRecords-1) / $maxResultsPerPage) + 1;

  $sql = "SELECT * FROM imjurCollections WHERE userID = $userID ORDER BY id ASC LIMIT $start, $maxResultsPerPage";
  $res = mysqli_query($link, $sql);
  
  $collections = [];
  for($i=0; $i<mysqli_num_rows($res); ++$i){
    $row = mysqli_fetch_assoc($res);
    $private = json_decode($row['meta'])->{'private'};
    if(!$private || ($passhash && ($admin || $enabled))){
      $ar           = [];
      $ar['id']     = $row['id'];
      $ar['name']   = $row['name'];
      $ar['slug']   = decToAlpha($row['id']);
      $ar['userID'] = $userID;
      $ar['meta']   = [
                      'date'          => json_decode($row['meta'])->{'date'},
                      'description'   => json_decode($row['meta'])->{'description'},
                      'slugs'         => json_decode($row['meta'])->{'slugs'},
                      'upvotes'       => json_decode($row['meta'])->{'upvotes'},
                      'votesCast'     => json_decode($row['meta'])->{'votesCast'},
                      'private'       => $private,
                      'views'         => json_decode($row['meta'])->{'views'},
                      //'originalSlugs' => json_decode($row['meta'])->{'originalSlugs'},
                      'serverTZO'     => getServerTZOffset(),
      ];
      $collections[] = $ar;
    }
  }
  if(sizeof($collections)){
    echo json_encode([true, $collections, $totalPages]);
  }else{
    echo json_encode([false]);
  }
?>