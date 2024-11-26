#!/bin/bash
cd /home/whitehotrobot/efx.git
rm -rf /home/whitehotrobot/nameBasedRouting/g
mkdir /home/whitehotrobot/nameBasedRouting/g
cp /home/whitehotrobot/efx.git/. /home/whitehotrobot/nameBasedRouting/g/. -r
cd /home/whitehotrobot/nameBasedRouting
chmod 777 . -R
git add .
git commit -m 'sync'
cat ~/github_token
git push origin main
cd /home/whitehotrobot/efx.git
