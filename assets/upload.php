<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(0);

  require_once('db.php');
  require_once('functions.php');

  $ct            = 0;
  $links         = [];
  $types         = [];
  $dates         = [];
  $originalDates = [];
  $sizes         = [];
  $slugs         = [];
  $names         = [];
  $views         = [];
  $ids           = [];
  $origins       = [];
  $visibilities  = [];
  $originalSlugs = [];
  $error         = '';
  $success       = false;
  $maxFileSize   = 25000000;
  $uploadDir     = 'uploads';
  $resourceDir   = 'resources';
  if(sizeof($_FILES)){
    forEach($_FILES as $key => $val){
      $unlink = false;
      $tmp_name = $_FILES["uploads_$ct"]['tmp_name'];
      $slug = genAssetSlug();
      move_uploaded_file($tmp_name, "$resourceDir/$slug");
      $type = mime_content_type("$resourceDir/$slug");
      $continue = false;
      if($size = filesize("$resourceDir/$slug")){
        if($size < $maxFileSize){
          switch($type){
            case 'audio/wav': $continue = true; $suffix = 'wav';  break;
            case 'audio/x-wav': $continue = true; $suffix = 'wav';  break;
            case 'audio/mp3': $continue = true; $suffix = 'mp3';  break;
            case 'audio/mpeg': $continue = true; $suffix = 'mp3';  break;

            case 'image/jpg': $continue = true; $suffix = 'jpg'; break;
            case 'image/jpeg': $continue = true; $suffix = 'jpeg';  break;
            case 'image/png': $continue = true; $suffix = 'png';  break;
            case 'image/gif': $continue = true; $suffix = 'gif';  break;
            case 'image/webp': $continue = true; $suffix = 'webp';  break;

            case 'video/webm': $continue = true; $suffix = 'webm';  break;
            case 'video/mkv': $continue = true; $suffix = 'mkv';  break;
            case 'video/mp4': $continue = true; $suffix = 'mp4';  break;
          }
          if($continue){
            if($type == 'video/mp4' && strpos($_FILES["uploads_$ct"]["name"], '.mp3') !== false){
              $type = 'audio/mp3';
              $suffix = 'mp3';
            }
            if($type == 'video/webm' && strpos($_FILES["uploads_$ct"]["name"], '.mp3') !== false){
              $type = 'audio/mp3';
              $suffix = 'mp3';
            }
            $hash = hash_file('md5', "$resourceDir/$slug");
            
            $sql = "SELECT * FROM imjurUploads WHERE hash = \"$hash\"";
            $res = mysqli_query($link, $sql);
            if(mysqli_num_rows($res)){
              $row = mysqli_fetch_assoc($res);
              $originalSlug = $row['originalSlug'];
              $originalDate = $row['originalDate'];
              $unlink = true;
            }else{
              $originalSlug = $slug;
            }
            
            $id = alphaToDec($slug);
            $original_name = basename($_FILES["uploads_$ct"]["name"]);
            $meta = mysqli_real_escape_string($link, json_encode([
              "file size" => $size,
              "sender IP" => $_SERVER['REMOTE_ADDR'],
              "original name" => $original_name,
            ]));
            
            $userID = -1;

            $bmd = json_decode($_POST['batchMetaData']);
            if($bmd->{'loggedIn'}){
              $uID = mysqli_real_escape_string($link, $bmd->{'userID'});
              $passhash = mysqli_real_escape_string($link, $bmd->{'passhash'});
              $sql = "SELECT * FROM imjurUsers WHERE id = $uID AND passhash LIKE BINARY \"$passhash\"";
              $res = mysqli_query($link, $sql);
              if(mysqli_num_rows($res)){
                $userID = $uID;
              }
            }
            
            
            $description = '';
            $origin = mysqli_real_escape_string($link, "user file: $original_name");
            $name = $original_name;
  $sql = <<<SQL
  INSERT INTO imjurUploads (id, 
                            slug,
                            originalSlug,
                            meta,
                            name,
                            hash,
                            filetype,
                            origin,
                            userID,
                            upvotes,
                            votesCast,
                            views,
                            description
                            )VALUES(
                              $id,
                              "$slug",
                              "$originalSlug",
                              "$meta",
                              "$name",
                              "$hash",
                              "$type",
                              "$origin",
                              $userID,
                              0,
                              0,
                              0,
                              "$description"
                            )
  SQL;
            
            mysqli_query($link, $sql);
            $sql = "SELECT date FROM imjurUploads WHERE slug LIKE BINARY \"$slug\"";
            $res = mysqli_query($link, $sql);
            $date = mysqli_fetch_assoc($res)['date'];
            if($originalSlug == $slug){
              $originalDate = $date;
            }
            $sql = "UPDATE imjurUploads SET originalDate=\"$originalDate\" WHERE slug LIKE BINARY \"$slug\"";
            $success         = true;
            $links[]         = "$uploadDir/$slug.$suffix";
            $sizes[]         = $size;
            $types[]         = $type;
            $slugs[]         = $slug;
            $names[]         = $name;
            $dates[]         = $date;
            $originalDates[] = $originalDate;
            $ids[]           = $id;
            $views[]         = 0;
            $visibilities[]  = 1;
            $origins[]       = $origin;
            $originalSlugs[] = $originalSlug;
            if($unlink){
              unlink("$resourceDir/$slug");
            }else{
              rename("$resourceDir/$slug", "$resourceDir/$slug.$suffix");
            }
          }else{
            $error = "ERROR<br>one or more files had an unrecognized or unsupported file type";
            unlink("$resourceDir/$slug");
          }
        }else{
          $error = "ERROR<br>one or more files were too large. $maxFileSize max";
          unlink("$resourceDir/$slug");
        }
        $ct++;
      }
    }
  } else {
    $error = 'ERROR<br>no files were received.<br><br>This usually means that the transfer was blocked by the server due to one or more files being too large...<br><br>Check your file sizes';
  }
  
  echo json_encode([$success, $links, $sizes, $types, $ct, $error, $slugs, $originalSlugs, $origins, getServerTZOffset(), $views, $ids, $dates, $originalDates, $visibilities, $names]);
?>