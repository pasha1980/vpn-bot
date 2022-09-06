#!/bin/bash

/usr/local/bin/wait-for-it db:3306

/var/www/app/bin/console cache:clear
/var/www/app/bin/console doctrine:schema:update --force --dump-sql

/usr/local/bin/supervisord -n -c /etc/supervisord.conf
