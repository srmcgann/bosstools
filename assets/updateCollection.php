<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(0);

/*
        userName: this.state.username,
        password: this.state.password,
        colData
*/

  require_once('db.php');
  require_once('functions.php');
  $data = json_decode(file_get_contents('php://input'));
  $userID = mysqli_real_escape_string($link, $data->{'userID'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});
  $colData = $data->{'colData'};

  $success = false;
  if($userID && $passhash){
    $sql = "SELECT * FROM imjurUsers WHERE id = $userID AND passhash LIKE BINARY \"$passhash\";";
    $res = mysqli_query($link, $sql);
  }
  if(mysqli_num_rows($res)){
    
    $name = mysqli_real_escape_string($link, $colData->{'name'});
    $id = mysqli_real_escape_string($link, $colData->{'id'});
    $slug = decToAlpha($id);
    $sql = "SELECT * FROM imjurCollections WHERE id = $id";
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      $description = $colData->{'description'}; //escaped later
      $slugs = $colData->{'slugs'};
      $nSlugs = [];
      
      // cull slugs which no longer exist (deleted)
      forEach($slugs as $slug_){
        $sql = "SELECT id FROM imjurUploads WHERE slug LIKE BINARY \"$slug_\"";
        $res2 = mysqli_query($link, $sql);
        if(mysqli_num_rows($res2)) $nSlugs[] = $slug_;
      }
      $slugs = $nSlugs;
      
      /*$originalSlugs = [];
      forEach($slugs as $slug){
        $sql = "SELECT originalSlug FROM imjurUploads WHERE slug LIKE BINARY \"$slug\"";
        $res = mysqli_query($link, $sql);
        $originalSlugs[] = $row['originalSlug'];
      }*/
      $private               = $colData->{'private'};
      $oMeta                 = json_decode($row['meta']);
      $meta                  = [];
      $meta['description']   = $description;
      $meta['slugs']         = $slugs;
      $meta['private']       = $private;
      //$meta['originalSlugs'] = $originalSlugs;
      $meta['date']          = $oMeta->{'date'};
      $meta['upvotes']       = $oMeta->{'upvotes'};
      $meta['votesCast']     = $oMeta->{'votesCast'};
      $meta['views']         = $oMeta->{'views'};
      $meta['serverTZO']     = $oMeta->{'serverTZO'};

      $meta_ = $meta;
      $meta = mysqli_real_escape_string($link, json_encode($meta));
      $sql = "UPDATE imjurCollections SET slug = \"$slug\", name = \"$name\", meta = \"$meta\" WHERE userID = $userID AND id = $id";
      if(mysqli_query($link, $sql)){
        $success           = true;
        $ar                = [];
        $ar['id']          = $id;
        $ar['name']        = $name;
        $ar['slug']        = $slug;
        $ar['userID']      = $userID;
        $ar['meta']        = $meta_;
        $updatedCollection = $ar;
        echo json_encode([$success, $updatedCollection]);
      }else{
        echo json_encode([$success, 1, $sql]);
      }
    }else{
      echo json_encode([$success, 2, $sql]);
    }
  }else{
    echo json_encode([$success, 3, $sql]);
  }
?>