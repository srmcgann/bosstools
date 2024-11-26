#!/bin/bash
cd /home/whitehotrobot/boss.mindhackers.org.git
rm -rf /home/whitehotrobot/nameBasedRouting/d
mkdir /home/whitehotrobot/nameBasedRouting/d
cp /home/whitehotrobot/boss.mindhackers.org.git/. /home/whitehot/ /home/whitehotrobot/nameBasedRouting/d/. -r
cd /home/whitehotrobot/nameBasedRouting
chmod 777 . -R
git add .
git commit -m 'sync'
cat ~/github_token
git push origin main
cd /home/whitehotrobot/boss.mindhackers.org.git
