#!/bin/sh

# Note: Don't create files containing dot(.)

cp -R /root/cron/1min /etc/periodic/1min
cp -R /root/cron/1hour /etc/periodic/1hour

chmod +x /etc/periodic/1min/*
chmod +x /etc/periodic/1hour/*

echo "* * * * * run-parts /etc/periodic/1min" | crontab -
(crontab -l; echo "0 * * * * run-parts /etc/periodic/1hour") | crontab -

touch /var/log/cron.log

crond

exec tail -f /var/log/cron.log