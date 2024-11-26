<?php
  require('db.php');
  $sql = "SELECT * FROM imjurServers";
  $res = mysqli_query($link, $sql);
  $servers = [];
  for($i=0; $i<mysqli_num_rows($res); ++$i){
    $row = mysqli_fetch_assoc($res);
    if($row['active']) $servers[]=$row['actualURL'];
  }
  $servers = json_encode($servers);
?>
<!DOCTYPE html>
<html>
  <head>
    <style>
      /* latin-ext */
      @font-face {
        font-family: 'Courier Prime';
        font-style: normal;
        font-weight: 400;
        font-display: swap;
        src: url(https://fonts.gstatic.com/s/courierprime/v9/u-450q2lgwslOqpF_6gQ8kELaw9pWt_-.woff2) format('woff2');
        unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
      }
      /* latin */
      @font-face {
        font-family: 'Courier Prime';
        font-style: normal;
        font-weight: 400;
        font-display: swap;
        src: url(https://fonts.gstatic.com/s/courierprime/v9/u-450q2lgwslOqpF_6gQ8kELawFpWg.woff2) format('woff2');
        unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
      }
      body, html{
        margin: 0;
        background: #000;
        color: #fff;
        font-family: Courier Prime;
        width: 100vw;
        height: 100vh;
      }
      #deployButton{
        border: none;
        background: #600;
        color: #f88;
        border-radius: 10px;
        font-size: 16px;
        min-width: 200px;
        padding: 5px;
        border: 1px solid #0008;
        position: fixed;
        margin-top: -5px;
        left: 0;
        margin-left: 250px;
        cursor: pointer;
      }
      #header{
        position: fixed;
        top: 0;
        left: 0;
        height: 20px;
        font-size: 20px;
        z-index: 10;
        background: linear-gradient(90deg, #000, #f00);
        border: 1px solid #333;
        padding: 10px;
        width: 100vw;
        display: inline-block;
      }
      .codeInput:focus{
        outline: none;
      }
      .codeInput{
        width: calc(100vw - 10px);
        height: calc(100vh - 50px);
        position: fixed;
        color: #08f;
        margin-top: 42px;
        border: 1px solid #40f;
        background: #111;
        display: block;
      }
      #outputModal{
        position: fixed;
        width: 100vw;
        height: 25vh;
        z-index: 1000;
        background: #123e;
        color: #fff;
        display: none;
      }
      #output{
        width: 100%;
        height: calc(100vh - 50px);
        margin: 0;
        margin-top: 5px;
        margin-bottom: 25px;
        border: 1px solid 333;
        background: #000;
        color: #fff;
        overflow-x: hidden;
        overflow-y: auto;
        word-break: break-all;
        display: inline-block;
      }
    </style>
  </head>
  <body>
    <div id="header">
      deploy PHP script
      <button id="deployButton" title="deploy (below script) to all ORBS servers" onclick="conf()">deploy</button>
    </div>
    <textarea spellcheck="false" class="codeInput" placeholder="text"></textarea>
    <div id="outputModal">
        <center><=== SERVER RESPONSES ===></center><br>
      <div id="output">
      </div>
    </div>
    <script>
      codeInput = document.querySelectorAll(".codeInput")[0]
      output = document.querySelector('#output')
      serverList = (<?=$servers?>).filter(v=>{
        return v //window.location.hostname.toLowerCase() == v.split('//')[1].replaceAll('/','').toLowerCase()
      }).map(v=>{
        console.log('server:', v)
        return v.split('//')[1].replaceAll('/','')
      })

      async function deploy(pass){
        sendData = {
          pass,
          source: codeInput.value
        }
        output.innerHTML = ''
	await serverList.map(async function (server){
	console.log(`//${server}/run/runScript.php`)
          await fetch(`//${server}/run/runScript.php`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(sendData),
          }).then(res=>res.text()).then(data=>{
            if(data){
              outputModal.style.display = 'block'
              output.innerHTML += `SERVER: ${server}<br>RESPONSE -><br><br><span style="color:#4f8;">`+data.replaceAll("\n",'<br>')+"</span><br><br>"
              //setTimeout(()=>{
              //  outputModal.style.display = 'none'
              //}, 1200000)
            }
          })
        })
      }
    
      async function conf(){
        if(pass=prompt('WARNING, THIS CAN MODIFY THE SERVERS\n\nARE YOU SURE YOU WANT TO CONTINUE?\n\n IF SO, ENTER PASSWORD...')){
          await deploy(pass)
        }
      }
    </script>
  </body>
</html>
