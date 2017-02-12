#!/bin/sh

cp -R /root/1min /etc/periodic/1min

chmod +x /etc/periodic/1min/*

echo "* * * * * run-parts /etc/periodic/1min" | crontab -

exec crond -f