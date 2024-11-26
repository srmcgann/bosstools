<?php
  require_once('db.php');
  
  //maintenance
  $sql = "SELECT * FROM imjurUploads WHERE userID = -1 AND DATEDIFF(date, NOW()) > 1";
  $res = mysqli_query($link, $sql);
  for($i=0; $i<mysqli_num_rows($res); ++$i){
    $row          = mysqli_fetch_assoc($res);
    $originalSlug = $row['originalSlug'];
    $slug         = $row['slug'];
    $uploadID     = $row['id'];
    $sql = "DELETE FROM imjurUploads WHERE id = $uploadID AND userID = -1";
    mysqli_query($link, $sql);
    $sql = "SELECT * FROM imjurUploads WHERE originalSlug LIKE BINARY \"$originalSlug\"";
    $res2 = mysqli_query($link, $sql);
    if(mysqli_num_rows($res2) == 0 && $originalSlug && strlen($originalSlug) > 1 && $slug === $originalSlug){
      forEach(glob("resources/$originalSlug.*") as $file){
        unlink($file);
      }
    }
    $sql = "DELETE FROM imjurVotes WHERE uploadID = $uploadID";
    mysqli_query($link, $sql);
    $sql = "DELETE FROM imjurComments WHERE uploadID = $uploadID";
    mysqli_query($link, $sql);
  }
  //
  
  function sortFunc(&$ar, $prop){
    switch($prop){
      case 'updated':
        function sf($a, $b){
          if($a->{'updated'} == $b->{'updated'}) return 0;
          return date($a->{'updated'}) < date($b->{'updated'}) ? -1 : 1;
        }
      break;
      default:
        function sf($a, $b){
          global $prop;
          if($a[$prop] == $b[$prop]) return 0;
          return $a[$prop] < $b[$prop] ? -1 : 1;
        }
      break;
    }
    uasort($ar, 'sf');
  }
  
  function getServerTZOffset () {
    $tz = date_default_timezone_get();
    $t = new DateTimeZone("$tz");
    return $t->getOffset(new DateTime('now'));
  }
  
  function alphaToDec($val){
    $pow=0;
    $res=0;
    while($val!=""){
      $cur=$val[strlen($val)-1];
      $val=substr($val,0,strlen($val)-1);
      $mul=ord($cur)<58?$cur:ord($cur)-(ord($cur)>96?87:29);
      $res+=$mul*pow(62,$pow);
      $pow++;
    }
    return $res;
  }

  function decToAlpha($val){
    $alphabet="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $ret="";
    while($val){
      $r=floor($val/62);
      $frac=$val/62-$r;
      $ind=(int)round($frac*62);
      $ret=$alphabet[$ind].$ret;
      $val=$r;
    }
    return $ret==""?"0":$ret;
  }
 
	function exists($url){
    if(strpos($url, '.') !== false){
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_NOBODY, true);
      curl_exec($ch);
      $r = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);
      return $r==200?1:0;
    }else{
      return false;
    }
	}

	function retrieve_remote_file_size($url){

		/*
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		curl_setopt($ch, CURLOPT_NOBODY, TRUE);
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		$data = curl_exec($ch);
		$size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
		curl_close($ch);
		return $size;
		*/
		$head = array_change_key_case(get_headers($url, TRUE));
		return $head['content-length'];
	}

  function genAssetSlug(){
    global $link;
    $rndmax = getrandmax();
    do{
      $newid = floor($rndmax/2+rand()/2);
      $sql = "SELECT id FROM imjurUploads WHERE id = $newid";
      $res = mysqli_query($link, $sql);
    }while(mysqli_num_rows($res));
    return decToAlpha($newid);
  }
  
  function genCollectionSlug(){
    global $link;
    $rndmax = getrandmax();
    do{
      $newid = floor($rndmax/2+rand()/2);
      $sql = "SELECT id FROM imjurCollections WHERE id = $newid";
      $res = mysqli_query($link, $sql);
    }while(mysqli_num_rows($res));
    return decToAlpha($newid);
  }
  
  function getSuffix($filetype){
    $suffix = "";
    switch($filetype){
      case 'audio/wav': $suffix = 'wav';  break;
      case 'audio/x-wav': $suffix = 'wav';  break;
      case 'audio/mp3': $suffix = 'mp3';  break;
      case 'audio/mpeg': $suffix = 'mp3';  break;

      case 'image/jpg': $suffix = 'jpg'; break;
      case 'image/jpeg': $suffix = 'jpeg';  break;
      case 'image/png': $suffix = 'png';  break;
      case 'image/gif': $suffix = 'gif';  break;
      case 'image/webp': $suffix = 'webp';  break;

      case 'video/webm': $suffix = 'webm';  break;
      case 'video/mkv': $suffix = 'mkv';  break;
      case 'video/mp4': $suffix = 'mp4';  break;
    }
    return $suffix;
  }
  
  function checkUserNameAvailability($userName){
    global $link;
    $sql='SELECT * FROM imjurUsers WHERE name LIKE "'.$userName.'"';
    $res = mysqli_query($link, $sql);
    return mysqli_num_rows($res) === 0;
  }
?>