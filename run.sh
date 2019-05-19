#!/bin/bash
select=$1;

[ -z "$select" ] && select="dev"

if [ "$select" = "dev" -o "$select" = "global" -o "$select" = "stop" ]
then
    if [ "$select" = "dev" ]
    then
        php bin/console server:start
        docker-compose up -d
    fi

    if [ "$select" = "global" ]
    then
        php bin/console server:start 0.0.0.0:8000
        docker-compose up -d
    fi

    if [ "$select" = "stop" ]
    then
        php bin/console server:stop
        docker-compose stop
    fi
else
    echo "Wrong argument. Allowed only [dev, global, stop]";
fi
