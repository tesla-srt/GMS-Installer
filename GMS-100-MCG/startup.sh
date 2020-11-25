#!/bin/sh

. /etc/functions.sh

########### Insert you own stuff for bootup here ###############

#edit this with your app name
FILE1 = /data/custom/scripts/scripts.tar
FILE2 = /data/custom/html/webfiles.tar

/usr/sbin/lighttpd -f /data/custom/webserver.conf

nice -19 php /data/custom/scripts/actions/power_up.php

if test -f "/data/custom/scripts.tar"; then
    rm /data/custom/scripts.tar
fi
 
if test -f "/data/custom/webfiles.tar"; then
    rm /data/custom/webfiles.tar
fi

################################################################
exit 0
