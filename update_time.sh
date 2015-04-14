#!/bin/bash

if ! [ $(ping -q -c 1 "google.de" 2>&1 | grep "1 received" | sed "s/.*\(1\) received.*/\1/") ] ||
   ! [ $(ping -q -c 1 "bing.de"  2>&1 | grep "1 received" | sed "s/.*\(1\) received.*/\1/") ]; then
    echo Not alive
else
    echo alive
    /etc/init.d/ntp stop
    /usr/sbin/ntpdate -u zeit.fu-berlin.de
    /etc/init.d/ntp start
fi
