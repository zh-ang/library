#!/bin/bash

#if [ ${1:0:1} == "/" ]; then
#    FILENAME=${1/$HOME/~};
#else
#    FILENAME=${PWD/$HOME/~}/$1;
#fi

FILENAME="/var/log/nginx/error.log";
INTERVAL="60";
MAILADDR="jay@easilydo.com"

if [ -z "$INTERVAL" ]; then
    INTERVAL=60;
fi;

if [ -z "$HOSTNAME" ]; then
    HOSTNAME=$(hostname);
fi;

if [ -r "$FILENAME" ]; then
    echo "Moniter on $FILENAME";
else
    echo "ERROR: $FILENAME is not readable."
    echo;
    exit;
fi;

current=`wc -l "$FILENAME" | awk '{print$1}'`;
current_time=`date +%H:%M:%S`;
last="$current";
last_time="$current_time";

while true; do
    sleep "$INTERVAL";
    current=`wc -l "$FILENAME" | awk '{print$1}'`;
    current_time=`date +%H:%M:%S`;
    sample="";
    if [ "$current" -gt "$last" ]; then
        new=$(($current-$last));
        sample=`tail -$new "$FILENAME" | grep "FastCGI sent in stderr" | head -c 4K`;
    elif [ "$current" -lt "$last" ]; then
        sample=`cat "$FILENAME" | grep "FastCGI sent in stderr" | head -c 4K`
    fi;
    if [ -n "$sample" ]; then
        (
            echo "To: $MAILADDR";
            echo "From: monitor<eng@easilydo.com>";
            echo "Subject: Monitor on $FILENAME";
            echo "Content-type: text/plain; charser=\"utf-8\"";
            echo ;
            echo "File: $FILENAME";
            echo "Period: $last_time -- $current_time";
            echo "Sample: ";
            echo "$sample";
            echo ;
            echo "----------------------------------------";
            echo "Generate on $HOSTNAME";
        ) | sendmail -t
        echo "[$current_time] Sending mail.";
    fi;
    last="$current";
    last_time="$current_time"
done;
