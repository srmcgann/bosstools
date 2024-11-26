<?php
  //ini_set('display_errors', 1);
  //ini_set('display_startup_errors', 1);
  error_reporting(0);
  require('../db.php');
  require('../functions.php');
  
  function deliverAsset($row, $id, $originalSlug, $suffix){
    global $link;
    $filetype = $row['filetype'];
    $sql = "UPDATE imjurUploads SET views = views + 1 WHERE id = $id";
    mysqli_query($link, $sql);
    $originalSlug = $row['originalSlug'];
    $src = fopen("../resources/$originalSlug.$suffix", 'r');
    stream_set_blocking($src, true);
    header("Content-Type: $filetype");
    do{
      $data = fread($src, 1024*128);
      if($data){
        echo $data;
        flush();
        //ob_flush();
      }
    }while($data);
  }
  
  $asset = mysqli_real_escape_string($link, $_GET['asset']);
  if($asset){
    $slug = explode('.', $asset)[0];
    $suffix = explode('.', $asset)[1];
    $id = alphaToDec($slug);
    $sql = "SELECT * FROM imjurUploads WHERE id = $id";
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      $authorized = false;
      if($row['private'] == 0){
        $authorized = true;
      }else{ ?>
      
        <script>
          async function checkEnabled(loggedinUserID, loggedinUserName, passhash){
            if(loggedinUserName) {
              let sendData = {
                userName: loggedinUserName,
                passhash,
              }
              await fetch(`../checkEnabled.php`, {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                },
                body: JSON.stringify(sendData),
              })
              .then(res => res.json())
              .then(data => {
                if(!!(+data[0])){
                  if(+data[1] == loggedinUserID){
                    
                  }
                  /*this.state.loggedIn= true
                  this.state.maxResultsPerPage = +data[4]
                  this.state.numComments = +data[5]
                  this.state.loggedinUserID = +data[1]
                  this.state.loggedInUser.avatar = data[2]
                  this.state.username = this.state.regusername || this.state.loggedinUserName
                  this.setCookie()
                  this.state.loginPromptVisible = false
                  this.state.invalidLoginAttemp = false
                  if(+data[3]) this.state.isAdmin = true
                  this.state.fetchUserInfo(this.state.loggedinUserID)
                  */
                }else{
                  location.href = '/'
                }
              })
            }
          }
                  
          let loggedinUserName
          let username
          let passhash
          let loggedinUserID
          let l = (document.cookie).split(';').filter(v=>v.split('=')[0].trim()==='loggedinuser')
          if(l.length){
            loggedinUserName = l[0].split('=')[1]
            username = loggedinUserName
            let l2 = (document.cookie).split(';').filter(v=>v.split('=')[0].trim()==='token')
            if(l2.length){
              passhash = l2[0].split('=')[1]
              let l3 = (document.cookie).split(';').filter(v=>v.split('=')[0].trim()==='loggedinuserID')
              if(l3.length){
                loggedinUserID = +l3[0].split('=')[1]
                checkEnabled(loggedinUserID, username, passhash)
              }else{
                location.href='/'
              }
            }else{
              location.href='/'
            }
          } else {
            location.href='/'
          }
        </script>
        
        <?
      }
      if($authorized){
        deliverAsset($row, $id, $originalSlug, $suffix)
      }
    }
  }
?>