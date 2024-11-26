<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(0);
  require_once('db.php');
  require_once('functions.php');
  $data = json_decode(file_get_contents('php://input'));
  $userName = mysqli_real_escape_string($link, $data->{'userName'});
  $passhash = mysqli_real_escape_string($link, $data->{'passhash'});

  $success = false;
  $continue = false;
  if($userName && $passhash){
    $sql = "SELECT * FROM imjurUsers WHERE name LIKE \"$userName\" AND passhash LIKE BINARY \"$passhash\" AND admin = 1;";
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)) $continue = true;
  }
  if($continue){
    $uploadDir     = 'uploads';
    $resourceDir   = 'resources';
    $ct            = 0;
    $fileSlugs     = [];
    $fileSizes     = [];
    $suffixes      = [];
    $filetypes     = [];
    $fileDates     = [];
    $hrefs         = [];
    $users         = [];
    $orphans       = [];
    $footprint     = 0;
    forEach(glob("$resourceDir/*") as $file){
      if(strpos($file, 'index.php') === false){
        $slug = explode('.', explode('/', $file)[1])[0];
        $slugs[] = $slug;
        $fs =  filesize($file);
        $footprint += $fs;
        $fileSizes[] = $fs;
        $ft = mime_content_type($file);
        $filetypes[] = $ft;
        $suffixes[] = getSuffix($ft);
        $ct++;
        $sql = "SELECT * FROM imjurUploads WHERE originalSlug LIKE BINARY \"$slug\"";
        $res = mysqli_query($link, $sql);
        if(!mysqli_num_rows($res)){
          $orphans[] = $slug;
          $hrefs[] = '';
          $fileDates[] = filemtime($file);
        }else{
          $row = mysqli_fetch_assoc($res);
          $hrefs[] = $file;
          $fileDates[] = $row['date'];
        }
      }
    }
    $sql = "SELECT * FROM imjurUsers";
    $res = mysqli_query($link, $sql);
    for($i=0; $i<mysqli_num_rows($res); ++$i){
      $row = mysqli_fetch_assoc($res);
      $userID = $row['id'];
      $sql = "SELECT * FROM imjurUploads WHERE userID = $userID";
      $res2 = mysqli_query($link, $sql);
      $row['slugs']         = [];
      $row['originalSlugs'] = [];
      $row['suffixes']      = [];
      $row['fileSizes']     = [];
      $row['fileDates']     = [];
      $row['filetypes']     = [];
      $row['hrefs']         = [];
      for($j=0; $j<mysqli_num_rows($res2); ++$j){
        $row2 = mysqli_fetch_assoc($res2);
        $fs2 = getSuffix($row2['filetype']);
        $row['fileSizes'][]     = $fileSizes[array_search($row2['slug'], $slugs)];
        $row['hrefs'][]         = "$resourceDir/{$row2['slug']}.$fs2";
        $row['slugs'][]         = $row2['slug'];
        $row['originalSlugs'][] = $row2['originalSlug'];
        $row['filetypes'][]     = $row2['filetype'];
        $row['fileDates'][]     = $row2['date'];
        $row['suffixes'][]      = $fs2;
      }
      
      $users[] = $row;
    }
    $adminData = json_encode([
      "slugs"           => $slugs,
      "hrefs"           => $hrefs,
      "fileSizes"       => $fileSizes,
      "fileDates"       => $fileDates,
      "filetypes"       => $filetypes,
      "users"           => $users,
      "number assets"   => $ct,
      "footprint"       => $footprint,
      "orphaned assets" => $orphans,
    ]);
    $success = true;
    echo json_encode([$success, $adminData]);
  }else{
    echo json_encode([$success]);
  }
?>