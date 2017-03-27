#!/bin/sh

cp -R /root/1min /etc/periodic/1min
cp -R /root/1hour /etc/periodic/1hour

chmod +x /etc/periodic/1min/*
chmod +x /etc/periodic/1hour/*

echo "* * * * * run-parts /etc/periodic/1min" | crontab -
echo "0 * * * * run-parts /etc/periodic/1hour" | crontab -

exec crond -f