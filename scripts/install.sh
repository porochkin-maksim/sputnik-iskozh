#!/bin/bash

sudo chown -R "${USER:=$(/usr/bin/id -run)}:$USER" '../'

mkdir -p '.storage/db/mysql'
mkdir -p '.storage/redis'
mkdir -p '.storage/meilisearch'
mkdir -p 'vendor/laravel'
mkdir -p 'vendor/bin'

cp -r 'storage/vendor/sail' 'vendor/laravel'
cp -r 'storage/vendor/bin' 'vendor'

docker compose stop
docker compose up --build -d

sudo chown -R "${USER:=$(/usr/bin/id -run)}:$USER" '../'
sudo chmod 0777 -R '.storage/'
