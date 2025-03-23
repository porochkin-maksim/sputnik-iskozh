#!/bin/bash

sudo chown -R "${USER:=$(/usr/bin/id -run)}:$USER" '../'

mkdir -p '.storage/db/mysql'
mkdir -p '.storage/redis'
mkdir -p '.storage/meilisearch'
mkdir -p 'vendor/laravel'
mkdir -p 'vendor/bin'

cp -r 'storage/vendor/sail' 'vendor/laravel'
cp -r 'storage/vendor/bin' 'vendor'

if [ ! -f /var/www/html/.env ]; then cp /var/www/html/.env.example /var/www/html/.env; fi

sudo chown -R "${USER:=$(/usr/bin/id -run)}:$USER" '../'
sudo chmod 0777 -R '.storage/'
