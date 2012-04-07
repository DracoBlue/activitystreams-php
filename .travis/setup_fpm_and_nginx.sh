#!/bin/bash
apt-get install nginx
apt-get install php5-fpm php5-mysql
cd `dirname $0`
cp nginx.conf /etc/nginx/nginx.conf
/etc/init.d/nginx restart
/etc/init.d/php5-fpm restart
cp server.config.php ../server/local.config.php
cp client.config.php ../client/local.config.php
