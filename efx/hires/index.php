<?
  $url = (isset($_GET['i']) && $_GET['i'] && strlen($_GET['i']) > 5) ? str_replace("https:/","https://",str_replace('https:/', 'https://', $_GET['i'])) : 'https://jsbot.twilightparadox.com/uploads/1cUypu.mp4';
?>
<!DOCTYPE html>
<html>
  <head>
    <style>
      body, html{
        margin: 0;
        width: 100%;
        height: 100vh;
        overflow: hidden;
        background: #000;
      }
      #c{
        position: absolute;
        top: 50%;
        left: 50%;
        width: 100%;
        height: 100%;
        background: url(138BkZ.jpg);
        background-position: center center;
        background-size: fill;
        transform: translate(-50%, -50%);
      }
      .playButton{
        border: none;
        font-family: courier;
        color: #fff;
        background: #aff6;
        border-radius: 0px;
        width: 100px;
        font-size: 24px;
        z-index: 100;
        cursor: pointer;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
      }
  </style>
  </head>
  <body>
    <button onclick="hideButton()"  class="playButton">play</button>
    <canvas id=c></canvas>
    <script>
      playbackSpeed = window.location.href.toUpperCase().split('PLAYBACKSPEED=')[1]
      if(typeof playbackSpeed !== 'undefined' && playbackSpeed.length){
        playbackSpeed=+playbackSpeed.split('&')[0]
      } else {
        playbackSpeed = 1
      }
      hideButton=()=>{
        document.querySelectorAll('.playButton')[0].style.display='none'
        launch()
      }
      resolution = window.location.href.toUpperCase().split('RESOLUTION=')[1]
      if(typeof resolution !== 'undefined' && resolution.length){
        resolution=+resolution.split('&')[0]
      } else {
        resolution = 2.5
      }
      launch=()=>{
      vid=''
      c=document.querySelector('#c')
      c=document.querySelector('#c')
      x=c.getContext('2d')
      S=Math.sin
      C=Math.cos
      t=playing=0
      rsz=window.onresize=()=>{
        setTimeout(()=>{
          if(document.body.clientWidth > document.body.clientHeight*1.77777778){
            c.style.height = '100vh'
            setTimeout(()=>c.style.width = c.clientHeight*1.77777778+'px',0)
          }else{
            c.style.width = '100vw'
            setTimeout(()=>c.style.height = c.clientWidth/1.77777778 + 'px',0)
          }
          //c.width=1920
          //c.height=c.width/1.777777778
        },0)
      }
      rsz()

      Draw=()=>{
        if(!t){
          modes = []
          window.location.href.split('?').map(v=>{
            v.split('&').map(q=>{
              modes.push(q)
            })
          })

          hex_margin = 1.05

          HSVFromRGB = (R, G, B) => {
            let R_=R/256
            let G_=G/256
            let B_=B/256
            let Cmin = Math.min(R_,G_,B_)
            let Cmax = Math.max(R_,G_,B_)
            let val = Cmax //(Cmax+Cmin) / 2
            let delta = Cmax-Cmin
            let sat = Cmax ? delta / Cmax: 0
            let min=Math.min(R,G,B)
            let max=Math.max(R,G,B)
            let hue = 0
            if(delta){
              if(R>=G && R>=B) hue = (G-B)/(max-min)
              if(G>=R && G>=B) hue = 2+(B-R)/(max-min)
              if(B>=G && B>=R) hue = 4+(R-G)/(max-min)
            }
            hue*=60
            while(hue<0) hue+=360;
            while(hue>=360) hue-=360;
            return [hue, sat, val]
          }

          RGBFromHSV = (H, S, V) => {
            while(H<0) H+=360;
            while(H>=360) H-=360;
            let C = V*S
            let X = C * (1-Math.abs((H/60)%2-1))
            let m = V-C
            let R_, G_, B_
            if(H>=0 && H < 60)    R_=C, G_=X, B_=0
            if(H>=60 && H < 120)  R_=X, G_=C, B_=0
            if(H>=120 && H < 180) R_=0, G_=C, B_=X
            if(H>=180 && H < 240) R_=0, G_=X, B_=C
            if(H>=240 && H < 300) R_=X, G_=0, B_=C
            if(H>=300 && H < 360) R_=C, G_=0, B_=X
            let R = (R_+m)*256
            let G = (G_+m)*256
            let B = (B_+m)*256
            return [R,G,B]
          }
          
          // defaults
          cycle           = 0
          color_offset    = 0
          brightness      = 0
          saturation      = .5
          contrast        = 0
          hex             = 0
          dither          = 0
          halftone        = 0
          psychedelicize  = 0
          invert          = false
          monochrome      = false

          intensities={
            wavey: .75,
            twirl: .5,
            vignette: .5,
            scanlines: .9,
            matrix: .9,
            cycle,
            color_offset,
            brightness,
            hex,
            dither,
            halftone,
            saturation,
            contrast,
            psychedelicize,
            invert,
            monochrome,
          }
          modes=modes.map(v=>{
            v=(l=v.split('='))[0]
            if(l.length>1 && typeof +l[1] == 'number') intensities[v] = +l[1]
            return v
          })
          cycle           = intensities['cycle']
          color_offset    = intensities['color_offset']
          brightness      = intensities['brightness']
          saturation      = intensities['saturation']
          contrast        = intensities['contrast']
          hex             = intensities['hex']
          psychedelicize  = intensities['psychedelicize']
          invert          = intensities['invert']
          monochrome      = intensities['monochrome']
          modes = [...new Set(modes)]
          if(modes.indexOf('matrix') !== -1) modes = modes.filter(v=>v!='scanlines')
          if(modes.indexOf('twirl') !== -1) modes = modes.filter(v=>v!='wavey')
          tc = document.createElement('canvas')
          if(
            srcurl.toLowerCase().indexOf('/download') !== -1 ||            
            srcurl.toLowerCase().indexOf('.mp4') !== -1 ||
            srcurl.toLowerCase().indexOf('.webm') !== -1 ||
            srcurl.toLowerCase().indexOf('.mov') !== -1){
            type='vid'
            c.width = 1920/6*resolution|0
            c.height = 1080/6*resolution|0
            tc.width = c.width
            tc.height = c.height
            tcx = tc.getContext('2d', {willReadFrequently: true})
            src=[false, document.createElement('video')]
            src[1].oncanplay=()=>{
              src[0]=true
              if(c.height/src[1].videoHeight<c.width/src[1].videoWidth){
                hex_mag = c.height/resolution/src[1].videoHeight*1.3333
              }else{
                hex_mag = c.width/resolution/src[1].videoWidth
              }
              src[1].loop = true
              if(autoplay) src[1].muted = true
              src[1].play()
            }
          } else {
            if(modes.indexOf('wavey')!==-1 || modes.indexOf('twirl')!==-1){
              if(modes.indexOf('wavey')!==-1){
                modes = modes.filter(v=>v!=='wavey')
                modes = [...modes, 'wavey']
              }
              c.width = 1920/6*resolution|0
              c.height = 1080/6*resolution|0
            } else {
              c.width = 1920/6*resolution|0
              c.height = 1080/6*resolution|0
            }
            tc.width = c.width
            tc.height = c.height
            tcx = tc.getContext('2d')
          }
          if(type=='vid') src[1].defaultPlaybackRate = playbackSpeed
          console.log('setting playbackSpeed to = ' + playbackSpeed)
          src[1].src="/proxy.php/?url=<?=$url?>"
        }
      
        if(modes.indexOf('vignette')!==-1){
          modes = modes.filter(v=>v!=='vignette')
          modes = [...modes, 'vignette']
        }
        if(src[0]){
          if(hex){
            if(type != 'vid'){
              if(c.height/src[1].height<c.width/src[1].width){
                hex_mag = c.height/resolution/src[1].height*1.3333
              }else{
                hex_mag = c.width/resolution/src[1].width
              }
            }
            hex_buffer = document.createElement('canvas')
            hex_buffer.width=c.width/hex/hex_mag|0
            hex_buffer.height=c.height/hex/hex_mag|0
            hex_ctx = hex_buffer.getContext('2d', {willReadFrequently: true})
          }
          var w_, h_
          if(typeof src[1].videoWidth != 'undefined'){
            if(src[1].videoWidth/src[1].videoHeight>1.7777777778){
              w_=tc.width
              h_=tc.width/(src[1].videoWidth/src[1].videoHeight)
            } else {
              w_=tc.height*(src[1].videoWidth/src[1].videoHeight)
              h_=tc.height
            }
          }else{
            if(src[1].width/src[1].height>1.7777777778){
              w_=tc.width
              h_=tc.width/(src[1].width/src[1].height)
            } else {
              w_=tc.height*(src[1].width/src[1].height)
              h_=tc.height
            }
          }
          tcx.drawImage(src[1],tc.width/2-w_/2,tc.height/2-h_/2,w_,h_)
          if(hex) {
            w__=w_/hex*2
            h__=h_/hex*2
            hex_ctx.drawImage(src[1],hex_buffer.width/2-w__/2,hex_buffer.height/2-h__/2,w__,h__)
            hex_ref=hex_ctx.getImageData(0,0,hex_buffer.width,hex_buffer.height)
          }
          d2 = tcx.getImageData(0,0,tc.width,tc.height)
          a = d2.data
          if(modes.indexOf('dither')!==-1 || modes.indexOf('halftone')!==-1){
            tcx.fillStyle='#000'
            tcx.fillRect(0,0,c.width,c.height)
            let hts = .5
            if(typeof intensities['halftone'] == 'number') hts = intensities['halftone']
            if(typeof intensities['dither'] == 'number') hts = intensities['dither']
            spc = hts*100|0
            for(ct=i=0;i<d2.data.length;i+=4){
              X=(i/4|0)%c.width
              Y=(i/4/c.width|0)
              if(!(X%spc) && !(Y%spc)){
                red   = a[i+0]
                green = a[i+1]
                blue  = a[i+2]
                alpha = a[i+3]
                luminosity = (red+green+blue)/3 |0
                tcx.fillStyle=`rgb(${red},${green},${blue})`
                tcx.beginPath()
                s=luminosity/255*spc/2
                tcx.arc(X,Y,s,0,7)
                tcx.fill()
              }
            }
            d2=tcx.getImageData(0,0,tc.width,tc.height)
            a = d2.data //tcx.putImageData(ref,0,0)
          }
          for(ct=i=0;i<d2.data.length;i+=4){
            red   = a[i+0]
            green = a[i+1]
            blue  = a[i+2]
            alpha = a[i+3]
            
            
            if(invert) {
              red = 255-red
              green = 255-green
              blue = 255-blue
            }
            let hue, sat, lum;
            [hue, sat, lum] = HSVFromRGB(red, green, blue);
            lum_ = lum

            if(color_offset) {
              hue+=color_offset
            }
            if(cycle) {
              hue += t*60*cycle
            }
            if(saturation) {
              sat *= (1+saturation)
              let del1 = -saturation/2+.5
              let del2 = 1-saturation
              //lum = .5*del1+lum*del2
              lum=saturation<0?.5*sat+lum*(1-sat):lum
              sat = sat*((1+saturation)/2)
            }
            if(brightness) {
              lum += brightness/1.25-1/4
              sat -= brightness/2
            }
            if(contrast){
              let del=(1+contrast)
              del2=(1+contrast)/2
              s=(1.05*(contrast+1))/(1.05-contrast)
              lum=contrast<=0?.5*(1-del)+lum*del:((s*(lum-.5))+.5)*(1-sat)+sat*lum
              s=360/6*contrast
              hue=contrast<=0?hue:(hue/s+.5|0)*s
              sat=contrast<=0?del*sat+(sat*(1-del))*del:sat//*del2
            }
            if(psychedelicize){
              sat=saturation
              hue+=lum*(100*psychedelicize)**3/600
            }
            [red, green, blue] = RGBFromHSV(hue, sat, lum);
            if(monochrome){
              val=(red+green+blue)/3
              red=val
              green=val
              blue=val
            }

            a[i+0] = red|0
            a[i+1] = green|0
            a[i+2] = blue|0
            
            
            X=(i/4|0)%c.width
            Y=(i/4/c.width|0)
            ref=d2
            modes.map(v=>{
              switch(v){
                case 'matrix':
                  red   = a[i+0]
                  green = a[i+1]
                  blue  = a[i+2]
                  alpha = a[i+3]
                  luminosity = (red+green+blue)/3 |0
                  red   = 0
                  green = luminosity * (f=((1-intensities['matrix']/2)-S(p=Math.PI*2/c.height*(c.height/2-Y)*(35-S(t*4)*20)+Math.PI/2)*(1-(1-intensities['matrix']/2))))
                  blue  = green / 3 |0
                  a[i+0] = red
                  a[i+1] = green
                  a[i+2] = blue
                  a[i+3] = 255
                break
                case 'vignette':
                  red   = a[i+0]
                  green = a[i+1]
                  blue  = a[i+2]
                  alpha = a[i+3]
                  cols=[red, green, blue]
                  red   = red   * (f=((.5-S(p=Math.PI*2/c.width*X+Math.PI/2)/2)*(.5-S(p=Math.PI*2/c.height*Y+Math.PI/2)/2))**intensities['vignette'])
                  green = green * f
                  blue  = blue  * f
                  a[i+0] = red
                  a[i+1] = green
                  a[i+2] = blue
                  a[i+3] = 255
                break
                case 'scanlines':
                  red   = red   * (f=((1-intensities['scanlines']/2)-S(p=Math.PI*2/c.height*(c.height/2-Y)*(35-S(t*4)*20)+Math.PI/2)*(1-(1-intensities['scanlines']/2))))
                  green = green * f
                  blue  = blue  * f
                  a[i+0] = red
                  a[i+1] = green
                  a[i+2] = blue
                  //a[i+3] = 255
                break
              }
            })
            ct++
          }
          if(modes.indexOf('wavey') !== -1) {
            d1 = tcx.getImageData(0,0,tc.width,tc.height)
            for(ct=i=0;i<d2.data.length;i+=4){
              X=(i/4|0)%c.width
              Y=(i/4/c.width|0)
              ref=d2
              b=d1.data
              d=Math.hypot(c.width/2-X,c.height/2-Y)
              p=Math.atan2(c.width/2-X,c.height/2-Y)
              s=20*intensities['wavey']//(1+d/2000)
              tx=X+S(q=p)*(m=(1+S(d/(30+S(t)*25)-t*20)/2)*s*Math.min(1,d/100))
              ty=Y+C(q)*m
              n=((ty|0)*c.width+(tx|0))*4
              b[i+0] = a[n+0]
              b[i+1] = a[n+1]
              b[i+2] = a[n+2]
              //b[i+3] = 255
              ref=d1
              ct++
            }
          }
          if(modes.indexOf('twirl') !== -1) {
            d1 = tcx.getImageData(0,0,tc.width,tc.height)
            for(ct=i=0;i<d2.data.length;i+=4){
              X=(i/4|0)%c.width
              Y=(i/4/c.width|0)
              ref=d2
              b=d1.data
              d=Math.hypot(e=c.width/2-X,f=c.height/2-Y)
              p=Math.atan2(e,f)+Math.PI+2.5/(1+d**3.8/2e6)*S(t*5)*intensities['twirl']*2
              tx=c.width/2+S(p)*d
              ty=c.height/2+C(p)*d
              n=((ty|0)*c.width+(tx|0))*4
              b[i+0] = a[n+0]
              b[i+1] = a[n+1]
              b[i+2] = a[n+2]
              //b[i+3] = 255
              ref=d1
              ct++
            }
          }
          x.putImageData(ref,0,0)
          if(modes.indexOf('hex') !== -1){
            hex_cl      = (hex_buffer.width)|0
            hex_rw      = (hex_buffer.height)|0
            Array(hex_cl*(hex_rw+1)*2|0).fill().map((v,i) => {
              let X = X__   = ((i%hex_cl)+((i/hex_cl|0)%2?.5:0))*4*0.8660254037844386
              let Y = Y__   = (i/hex_cl|0)
              let g     = (Y__*hex_cl+X__|0)*4
              let red   = hex_ref.data[g+0]
              let green = hex_ref.data[g+1]
              let blue  = hex_ref.data[g+2]
              let fcol = `rgba(${red},${green},${blue},.4)`
              let sp_=c.width/hex_buffer.width
              X_ = X*sp_
              Y_ = Y*sp_
              x.beginPath()
              for(let m=6;m--;){
                X=X_+S(p=Math.PI*2/6*m+Math.PI/6)*hex*hex_mag*hex_margin/0.8660254037844386
                Y=Y_+C(p)*hex*hex_margin*hex_mag/0.8660254037844386
                x.lineTo(X,Y)
              }
              let scol='#ffffff10'
              if(scol){
                x.closePath()
                //x.globalAlpha=.2
                x.strokeStyle=scol
                x.lineWidth=1+hex/10|0
                //x.stroke()
                x.globalAlpha=1
                x.lineWidth/=4
                x.stroke()
              }
              if(fcol){
                x.fillStyle=fcol
                x.fill()
              }
            })
          }
        }

      t+=1/60
      //if(!(type=='img' && src[0] && modes.indexOf('wavey')==-1 && modes.indexOf('twirl')==-1)) 
      requestAnimationFrame(Draw)
    }

    Draw()
  }
  let srcurl = "<?=$url?>"
  if(
    srcurl.toLowerCase().indexOf('.jpg') !== -1 ||
    srcurl.toLowerCase().indexOf('.jpeg') !== -1 ||
    srcurl.toLowerCase().indexOf('.bmp') !== -1 ||
    srcurl.toLowerCase().indexOf('.tiff') !== -1 ||
    srcurl.toLowerCase().indexOf('.png') !== -1 ||
    srcurl.toLowerCase().indexOf('.gif') !== -1 ||
    srcurl.toLowerCase().indexOf('/drawforme') !== -1 ||
    srcurl.toLowerCase().indexOf('/preview') !== -1 ||
    srcurl.toLowerCase().indexOf('.ico') !== -1 ||
    srcurl.toLowerCase().indexOf('.apng') !== -1 ||
    srcurl.toLowerCase().indexOf('.svg') !== -1){
    type='img'
    src=[false, new Image()]
    src[1].onload=()=>{
      src[0]=true
    }
    hideButton()
  }
  autoplay=false
  if(!!(window.location.href.indexOf('autoplay')!==-1)) {
    autoplay=true
    setTimeout(()=>{hideButton()}, 0)
  }
</script>
  </body>
</html>

