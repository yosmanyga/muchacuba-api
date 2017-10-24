# Dev init

docker run -it --rm -v $(pwd)/app:/app -w /app --name node node:7.5.0-alpine sh
npm install

docker-compose \
-f docker/docker-compose.common.yml \
-f docker/docker-compose.dev.yml \
-p muchacuba \
up -d

cd api

composer install

cp api/config/parameters.dist.yml api/config/parameters.yml

chmod a+w api/var/cache

## Dev debug

Abrir https://localhost:3000 -> Advanced -> Proceed (unsafe)
Debug en unoo de los pedidos -> Enviar

docker exec -it muchacuba_php

php bin/app.php /internauta/process-requests

## Dev tests

### Behat

docker exec -it muchacuba_php sh

cd domain

../vendor/bin/behat

## Backup & restore

# En el container mongo prod
apk add --no-cache mongodb-tools
mongodump --db muchacuba
rm -rf /data/db/dump
mv dump /data/db

# En mi pc
scp -r root@muchacuba.com:/root/muchacuba/api/var/db/dump/ /home/yosmanyga/Work/Projects/cubalider/muchacuba/code/api/var/db

# En el container mongo dev
apk add --no-cache mongodb-tools
mongorestore --drop --db muchacuba /data/db/dump/muchacuba
rm -rf /data/db/dump/muchacuba/*
