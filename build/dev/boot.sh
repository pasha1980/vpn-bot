#!/bin/bash

/usr/local/bin/wait-for-it database:3306

/var/www/app/bin/console cache:clear
/var/www/app/bin/console doctrine:migrations:migrate -vv << EOF

EOF

/usr/local/bin/supervisord -n -c /etc/supervisord.conf
