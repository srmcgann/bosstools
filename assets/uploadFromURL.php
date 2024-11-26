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
  $originalSlugs = [];
  $visibilities  = [];
  $error         = '';
  $success       = false;
  $maxFileSize   = 100000000;
  $uploadDir     = 'uploads';
  $resourceDir   = 'resources';
  
  
  $data = json_decode(file_get_contents('php://input'));
  $url = $data->{'URL'};
  if($url){
    $bmd = $data->{'batchMetaData'};
    
    $unlink = false;
    //$tmp_name = $_FILES["uploads_$ct"]['tmp_name'];
    $slug = genAssetSlug();
    //move_uploaded_file($tmp_name, "$resourceDir/$slug");
    $name = "$resourceDir/$slug";

    $ok = 0;
    if(exists($url)){
      $path_parts = pathinfo($url);
      $ext=strtolower(substr($path_parts['extension'],0,strpos($path_parts['extension'],"?")?strpos($path_parts['extension'],"?"):1000));
      $originalName=substr($path_parts['basename'],0,strpos($path_parts['basename'],"?")?strpos($path_parts['basename'],"?"):1000);
      switch($ext){
        case "jpg": $ok=1; break;
        case "zip": $ok=1; break;
        case "jpeg": $ok=1; break;
        case "mp3": $ok=1; break;
        case "png": $ok=1; break;
        case "mkv": $ok=1; break;
        case "wav": $ok=1; break;
        case "gif": $ok=1; break;
        case "bmp": $ok=1; break;
        case "mp4": $ok=1; break;
        case "webp": $ok=1; break;
        case "webm": $ok=1; break;
        case "ogv": $ok=1; break;
        case "swf": $ok=1; break;
      }
      if($ok){
        $size=retrieve_remote_file_size($url);
        $oSize = $size;
        if($size>100000000){
          $error = "too big";
        }elseif($size){
          //set_time_limit(0);
          //$fp = fopen ( $name, 'w');
          $ch = curl_init(str_replace(" ","%20",$url));
          //echo $name;
          //curl_setopt($ch, CURLOPT_FILE, $fp);
          curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
          curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.82 Safari/537.36 OPR/79.0.4143.73',);
          $file = curl_exec($ch); 
          file_put_contents($name, $file);
          curl_close($ch);
          //fclose($fp);
          //echo $name;
        }else{
          $error = "not found";
        }
      }else{
        $error = "wrong type";
      }
    }else{
      $error = "not found";
    }

    
    if($ok){
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
            if($type == 'video/mp4' && strpos($url, '.mp3') !== false){
              $type = 'audio/mp3';
              $suffix = 'mp3';
            }
            if($type == 'video/webm' && strpos($url, '.mp3') !== false){
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
            //$originalName = basename($_FILES["uploads_$ct"]["name"]);
            $meta = mysqli_real_escape_string($link, json_encode([
              "file size" => $size,
              "sender IP" => $_SERVER['REMOTE_ADDR'],
              "original name" => $originalName,
              "original URL" => $url,
            ]));
            
            $userID = -1;

            //$bmd = json_decode($_POST['batchMetaData']);
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
            $origin = mysqli_real_escape_string($link, "web file: $url");
            $name   = $originalName;
            
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
            $visibilities[]  = 1;
            $originalDates[] = $originalDate;
            $ids[]           = $id;
            $views[]         = 0;
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
          $mfmb = floor($maxFileSize / 1024);
          $mfmb = floor($oSize / 1024);
          $error = "ERROR<br>one or more files were too large. $mfmb MB max<br><br>Your file was $mfmb2 MB!";
          unlink("$resourceDir/$slug");
        }
        $ct++;
      }
    } else {
      $error = 'ERROR<br>no files were received.<br><br>This usually means that the transfer was blocked by the server due to one or more files being too large...<br><br>Check your file sizes';
    }
  }    
  
  echo json_encode([$success, $links, $sizes, $types, $ct, $error, $slugs, $originalSlugs, $origins, getServerTZOffset(), $views, $ids, $dates, $originalDates, $visibilities, $names]);
?>