<?php 
  require("db.php");
  if(isset($_GET['ircID']) || isset($_GET['demoID'])){
    if(isset($_GET['ircID'])){
      $demoID=mysqli_real_escape_string($link,$_GET['ircID']);
      $sql="SELECT * FROM irc WHERE id=$demoID";
      $res=$link->query($sql);
      if(mysqli_num_rows($res)){
        $row=mysqli_fetch_assoc($res);
        $code=$row['ircJS'];
        $css=$row['ircCSS'];
        $html=$row['ircHTML'];
      }
    } else {
      $demoID=mysqli_real_escape_string($link,$_GET['demoID']);
      $sql="SELECT * FROM items WHERE id=$demoID";
      $res=$link->query($sql);
      if(mysqli_num_rows($res)){
        $row=mysqli_fetch_assoc($res);
        $code=$row['demoJS'];
        $css=$row['demoCSS'];
        $html=$row['demoHTML'];
      }
    }
?>
<!DOCTYPE html>
<html>
  <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <style>

      body,html{
        overflow:hidden;
        margin: 0;
      }
      canvas{
        width: 100%;
        height: 100%;
      }
      #c{
        width: 100%;
        height: 100%;
      }
      <?php echo $css?>
    </style>
      <script>
        window.addEventListener("message", receiveMessage, false);
        function receiveMessage(event){
		var origin = event.origin || event.originalEvent.origin;
		console.log(origin)
          if (origin !== "boss.mindhackers.org" && origin !== 'code.twilightparadox.com' ) return;
          message=event.data;
          var command=message.split(':')[0];
          var data=message.substr(command.length+1);
          switch(command){
            case "start":
              if(data !=""){
                if(typeof animationFrameHandle != "undefined"){
                  cancelAnimationFrame(animationFrameHandle);
                  delete animationFrameHandle;
                }
                document.body.removeChild(document.querySelector("#script-block"));
                document.querySelector("#error").innerHTML="";
                var newScript=document.createElement("script");
                newScript.id="script-block";
                newScript.text=data
                document.body.appendChild(newScript);
                
                try {
                  eval(data);
                } catch (e) {
                  c.style.display="none";
                  document.querySelector("#error").innerHTML=e;
                  throw e;
                };
              }else{
              }
              if(typeof animationFrameHandle == "undefined"){
                fpsInterval = 1000 / 60;
                then = window.performance.now();
                startTime = then;
              }
              break;
            case "stop":
              if(typeof animationFrameHandle != "undefined"){
                cancelAnimationFrame(animationFrameHandle);
                delete animationFrameHandle;
              }
              break;
          }
        }
      </script>
      <?php 
    }else{
      http_response_code(404);
      echo "404. :(";
      die();
    }
    ?>
  </head>
  <body>
    <?php echo $html?>
    <div id="error"></div>
    <script id="script-block">
      <?php echo $code?>;
      if(typeof c !== 'undefined'){
        c.width=1920
        c.height=1080
      }
    </script>
  </body>
</html>
