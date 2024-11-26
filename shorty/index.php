<?php
  function get_title($url){
    $str = file_get_contents($url);
    if(strlen($str)>0){
      $str = trim(preg_replace('/\s+/', ' ', $str));
      preg_match("/\<title\>(.*)\<\/title\>/i", $str, $title);
      return sizeof($title) > 1 ? $title[1] : '';
    }
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

	$slug=isset($_GET['slug']) ? $_GET['slug'] : '';
  $redirect=true;
	if(!$slug){
    //echo 'no slug!';
		//die();
    $redirect = false;
  }else{
    require('db.php');
	  $id = alphaToDec($slug);
	  $sql = "SELECT * FROM links WHERE id = $id";
	  $res = mysqli_query($link, $sql);
	  $row = mysqli_fetch_assoc($res);
	  $target = $row['target'];
  }
?>
<!DOCTYPE html>
<html>
  <?php if($redirect) { ?>
    <head>
      <?php
        $title = get_title($target);
        if($title){
          $meta = get_meta_tags($target);
          echo "<meta charset=\"UTF-8\">";
          if(isset($meta['description'])) echo "<meta name=\"description\" content=\"{$meta['description']}\">";
          if(isset($meta['keywords'])) echo "<meta name=\"keywords\" content=\"{$meta['keywords']}\">";
          if(isset($meta['author'])) echo "<meta name=\"author\" content=\"{$meta['author']}\">";
          if(isset($meta['viewport'])) echo "<meta name=\"viewport\" content=\"{$meta['viewport']}\">";
          if($title) echo "<title>$title</title>";
        }
      ?>
    </head>
    <body>
      <script>
        target = '<?php echo $target?>'
        slug = '<?php echo $slug?>'
        id = '<?php echo $id?>'
        console.log(slug, id)
        if(target) window.location.href = target
      </script>
    </body>
  <?php } else { ?>
    <head>
		  <title></title>
      <style>
        body, html{
          margin: 0;
          height: 100vh;
          background: linear-gradient(45deg, #000, #123);
          font-family: courier;
          font-size: 18px;
          color: #8ff;
          text-align: center;
          overflow: hidden;
        }
        .main{
          max-width: 800px;
          display: inline-block;
          position: absolute;
          top: 40%;
          left: 50%;
          transform: translate(-50%,-50%);
        }
        #targetInput{
          background: #000;
          border: none;
          outline: none;
          color: #cfc;
          width: 700px;
          text-align: center;
          border-bottom: 1px solid #4fc8;
          font-size: 14px;
          font-family: courier;
        }
        .validStatus{
          position: absolute;
          left: 50%;
          margin-top: -20px;
          transform: translate(-50%);
        }
        #shortenButton{
          border: none;
          background: #333;
          border-radius: 10px;
          font-family: courier;
          color: #888;
          font-size: 24px;
          cursor: pointer;
        }
        .resultLink{
          text-decoration: none;
          color: #fff;
          background: #4f86;
          padding: 10px;
        }
        #resultDiv{
          position: absolute;
          margin-top: 50px;
          left: 50%;
          transform: translate(-50%);
          min-width: 500px;
        }
        .copyButton{
          display: none;
          width: 30px;
          height: 30px;
          background-image: url(clippy.png);
          cursor: pointer;
          z-index: 500;
          background-size: 90% 90%;
          position: absolute;
          left: calc(50% + 225px);
          background-position: center center;
          background-repeat: no-repeat;
          border: none;
          background-color: #8fcc;
          margin: 5px;
          margin-top: 115px;
          border-radius: 5px;
        }
        #copyConfirmation{
          display: none;
          position: absolute;
          width: 100vw;
          height: 100vh;
          top: 0;
          left: 0;
          background: #012d;
          color: #8ff;
          opacity: 1;
          text-shadow: 0 0 5px #fff;
          font-size: 46px;
          text-align: center;
          z-index: 1000;
        }
        #innerCopied{
          position: absolute;
          top: 50%;
          width: 100%;
          z-index: 1020;
          text-align: center;
          transform: translate(0, -50%) scale(2.0, 1);
        }
      </style>
    </head>
    <body>
      <script>
        copy = () => {
          var range = document.createRange()
          range.selectNode(document.querySelectorAll('.resultLink')[0])
          window.getSelection().removeAllRanges()
          window.getSelection().addRange(range)
          document.execCommand("copy")
          window.getSelection().removeAllRanges()
          let el = document.querySelector('#copyConfirmation')
          el.style.display = 'block';
          el.style.opacity = 1.5
          reduceOpacity = () => {
            if(+el.style.opacity > 0){
              el.style.opacity -= .02
              setTimeout(()=>{
                reduceOpacity()
              }, 10)
            }
          }
          reduceOpacity()
          setTimeout(()=>{
            el.style.opacity = 1
            el.style.display = 'none'
          }, 750)
        }
      </script>
      <div id="copyConfirmation"><div id="innerCopied">COPIED!</div></div>
      <div class="main">
        <input
          oninput="validate()"
          onkeydown="shortenMaybe(event)"
          autofocus
					spellcheck=false
          id="targetInput"
          type="text"
          placeholder="enter URL to shorten..."
        ><br><br>
        <div class="validStatus" id="validStatus"></div><br>
        <button id="shortenButton" onclick="shorten()">shorten</button>
        <div id="resultDiv"></div>
        <button onclick="copy()" class="copyButton"></button>
      </div>
      <script>
        targetInput = document.querySelector('#targetInput')
        shortenButton = document.querySelector('#shortenButton')
        resultDiv = document.querySelector('#resultDiv')
        function validURL(str) {
          var regex = /(?:https?):\/\/(\w+:?\w*)?(\S+)(:\d+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/;
          if(!regex .test(str)) {
            return false;
          } else {
            return true;
          }
        }
        validate=()=>{
          targetInput.value=targetInput.value.trim()
          let val = targetInput.value
          let valid = false
          valid = validURL(val)
          if(val){
            validStatus.style.background = valid ? '#0f84' : '#f004'
            validStatus.style.color = valid ? '#8ff' : '#faa'
            validStatus.innerHTML = valid ? 'URL is valid!' : 'URL is <b>NOT</b> valid'
            shortenButton.style.color = valid ? '#4f8' : '#888'
            shortenButton.style.background = valid ? '#8f84' : '#333'
          } else {
            validStatus.innerHTML =''
            shortenButton.style.color = valid ? '#4f8' : '#888'
            shortenButton.style.background = valid ? '#8f84' : '#333'
          }
          return valid
        }
        shortenMaybe=e=>{
          if(e.keyCode==13 && validate()){
            shorten()
          }
        }
        shorten=()=>{
          if(!validate()) return
          let link = targetInput.value
          sendData = { link }
          fetch('shorty.php',{
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(sendData),
          }).then(res=>res.text()).then(data=>{
            if(data){
              let link = document.createElement('a')
              link.className = 'resultLink'
              link.target = '_blank'
              document.querySelectorAll('.copyButton')[0].style.display='inline-block'
              link.innerHTML = link.href = location.origin + '/shorty/?slug=' + data
              resultDiv.innerHTML = 'your link<br><br>'
              resultDiv.appendChild(link)
              targetInput.value = ''
              validStatus.innerHTML =''
              shortenButton.style.color = '#888'
              shortenButton.style.background = '#333'
            } else {
              alert('crap.')
            }
          })
        }
      </script>
    </body>
  <?php } ?>
</html>