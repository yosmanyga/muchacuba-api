#!/bin/sh

# Note: Don't create files containing dot(.)

cp -R /root/cron/1min /etc/periodic/1min

chmod +x /etc/periodic/1min/*

echo "* * * * * run-parts /etc/periodic/1min" | crontab -

touch /var/log/cron.log

crond

exec tail -f /var/log/cron.log