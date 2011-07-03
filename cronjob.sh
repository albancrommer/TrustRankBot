#!/bin/bash

lockfile=/tmp/trustrankbot.lock
# 
# if [ -f /tmp/trustrankbot.lock ]
# then
# 
# [ -e /proc/$(cat ${lockfile}) ] && exit 100
# fi
# 
# echo $$ >> ${lockfile}

DIR="$( cd "$(dirname "$0")" && pwd )"
echo "The present script is running from $DIR with $$ pid";

# You will certainly want to modify this
/usr/local/php5/bin/php "$DIR/bot.php"

rm -f ${lockfile}
