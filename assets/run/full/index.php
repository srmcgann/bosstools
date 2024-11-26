<?php
  require('../db.php');
  $sql = "SELECT * FROM orbsMirrors";
  $res = mysqli_query($link, $sql);
  $servers = [];
  for($i=0; $i<mysqli_num_rows($res); ++$i){
    $row = mysqli_fetch_assoc($res);
    $servers[]=$row['actualURL'];
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
      #main{
        overflow-x: hidden;
        overflow-y: auto;
        position: fixed;
        height: calc(100vh - 40px);
        margin-top: 40px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-evenly;
      }
      iframe:focus{
        outline: none;
      }
      iframe{
        min-width: 475px;
        min-height: 300px;
        margin: 5px;
        margin-bottom: 0;
        border: none;
      }
      .frameDiv{
        margin: 10px;
        padding: 5px;
        border: 1px solid #f006;
        background: #f003;
        color: #f00;
      }
      .codeInput:focus{
        outline: none;
      }
      .codeInput{
        width: calc(100vw - 60px);
        position: relative;
        color: #08f;
        min-height: 100px;
        margin-top: 5px;
        border: 1px solid #40f;
        background: #111;
        display: block;
      }
      .reloadFrameButton{
        background: #600;
        color: #f88;
        border-radius: 5px;
        font-size: 16px;
        min-width: 45px;
        padding: 0;
        border: 1px solid #0008;
        position: absolute;
        z-index: 5000;
        cursor: pointer;
        line-height: 16px;
        padding-top: 2px
      }
    </style>
  </head>
  <body>
    <div id="header">
      FULL deploy [PHP script]
    </div>
    <div id="main">
    <textarea class="codeInput" spellcheck="false" placeholder="scratch area"></textarea><br>
    </div>
    <script>
      main = document.querySelector('#main')
      serverList = (<?=$servers?>).map(v=>{
        console.log('server:', v)
        return v.split('//')[1].replaceAll('/','')
      })
      serverList.map((server, idx) => {
        let newFrameDiv = document.createElement('div')
        let newFrame = document.createElement('iframe')
        newFrame.id = 'iframe' + idx
        newFrameDiv.className = 'frameDiv'
        let reloadButton = document.createElement('button')
        reloadButton.className = 'reloadFrameButton'
        reloadButton.innerHTML = "⤾"
        reloadButton.title = 'reload this server frame'
        newFrameDiv.appendChild(reloadButton)
        reloadButton.id = "reloadButton" + idx
        newFrame.src = `https://${server}/run`
        let serverSpan = document.createElement('span')
        serverSpan.style.marginLeft = '50px'
        serverSpan.innerHTML = server
        newFrameDiv.appendChild(serverSpan)
        newFrameDiv.innerHTML += '<br>'
        newFrameDiv.appendChild(newFrame)
        main.appendChild(newFrameDiv)
      })
      serverList.map((server, idx) => {
        let el = document.querySelector('#reloadButton' + idx)
        el.onclick = () =>{
          let frame = document.querySelector(`#iframe${idx}`)
          frame.src = frame.src
        }
      })
    </script>
  </body>
</html>