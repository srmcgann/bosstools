<?php
  error_reporting(0);
  require_once('db.php');
  require_once('functions.php');
  $userID = '';
  $passhash = '';
  $data = json_decode(file_get_contents('php://input'));
  $preval = $data->{'userID'};
  if($preval) $userID = mysqli_real_escape_string($link, $preval);
  $preval = $data->{'passhash'};
  if($preval) $passhash = mysqli_real_escape_string($link, $preval);
  $collectionID = mysqli_real_escape_string($link, $data->{'collectionID'});

  // uncomment for paginated collections

  //$page = mysqli_real_escape_string($link, $data->{'page'});
  //$overrideMaxResults = mysqli_real_escape_string($link, $data->{'maxResultsPerPage'});
  //if($overrideMaxResults) $maxResultsPerPage = $overrideMaxResults;
  //$start = $maxResultsPerPage * $page;
  
  $enabled = 0;
  if($passhash){
    $sql = "SELECT * FROM imjurUsers WHERE id = $userID AND passhash LIKE BINARY\"$passhash\"";
    $res = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($res);
    $admin = $row['admin'];
    $enabled = $row['enabled'];
  }

  //$sql="SELECT id FROM imjurCollections WHERE id = $collectionID AND (($enabled AND userID = $userID) OR $admin OR private = 0)";
  //$res = mysqli_query($link, $sql);
  //$totalRecords = mysqli_num_rows($res);
  //$totalPages = floor(($totalRecords-1) / $maxResultsPerPage) + 1;
  $totalPages = 1;
  
  //$sql = "SELECT * FROM imjurCollections WHERE id = $collectionID AND (($enabled AND userID = $userID) OR $admin OR private = 0) ORDER BY id ASC LIMIT $start, $maxResultsPerPage";
  
  $sql = "SELECT * FROM imjurCollections WHERE id = $collectionID";
  $res = mysqli_query($link, $sql);
  
  
  $collections = [];
  for($i=0; $i<mysqli_num_rows($res); ++$i){
    $row = mysqli_fetch_assoc($res);
    $meta = json_decode($row['meta']);
    $private = intval($meta->{'private'});
    $owner = intval($row['userID']) == intval($userID);
    if(!$private || ($passhash && ($admin || $enabled))){
      $ar           = [];
      $ar['id']     = $collectionID;
      $ar['name']   = $row['name'];
      $ar['slug']   = decToAlpha($collectionID);
      $ar['userID'] = $userID;
      $views        = intval($meta->{'views'})+1;
      $ar['meta']   = [
                      'date'          => $meta->{'date'},
                      'description'   => $meta->{'description'},
                      'slugs'         => $meta->{'slugs'},
                      'upvotes'       => $meta->{'upvotes'},
                      'votesCast'     => $meta->{'votesCast'},
                      'private'       => $private,
                      'views'         => $views,
                      //'originalSlugs' => $meta->{'originalSlugs'},
                      'serverTZO'     => getServerTZOffset(),
      ];
      $collections[] = $ar;
    }
  }
  if(sizeof($collections)){
    if(!$owner){
      $newSlugs = [];
      forEach($collections[0]['meta']['slugs'] as $slug){
        $sql = "SELECT private FROM imjurUploads WHERE slug LIKE BINARY \"$slug\"";
        $res2 = mysqli_query($link, $sql);
        $row = mysqli_fetch_assoc($res2);
        if(!intval($row['private'])){
          $newSlugs[] = $slug;
        }
      }
      $collections[0]['meta']['slugs'] = $newSlugs;
    }
    
    // increment views
    $newMeta = mysqli_real_escape_string($link, json_encode($ar['meta']));
    $sql = "UPDATE imjurCollections SET meta = \"$newMeta\" WHERE id = $collectionID";
    mysqli_query($link, $sql);
    echo json_encode([true, $collections[0], $totalPages]);
  }else{
    echo json_encode([false, $sql]);
  }
?>