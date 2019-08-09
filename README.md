git clone git@github.com:yosmanyga/muchacuba-api.git muchacuba-api

cd muchacuba-api



# Dev init

docker-compose \
-f docker/common.yml \
-f docker/dev.yml \
-p muchacuba_api \
up -d

# Prod

docker network create muchacuba

docker-compose \
-f docker/common.yml \
-f docker/prod.yml \
-p muchacuba_api \
up -d

composer install

cp config/parameters.dist.yml config/parameters.yml

chmod a+w var/cache

## Dev debug

Abrir https://localhost:3000 -> Advanced -> Proceed (unsafe)
Abrir https://muchacuba.com/#/internauta/list-logs
Debug en uno de los pedidos -> Enviar

docker exec -it muchacuba_api_php sh

php bin/app.php /internauta/manual-push-request "yosmanyga@gmail.com" "horoscopo@muchacuba.com" "sagitario"
php bin/app.php /internauta/manual-push-request "yosmanyga@gmail.com" "traducir@muchacuba.com" "123 probando"
php bin/app.php /internauta/manual-push-request "yosmanyga@gmail.com" "lyrics@muchacuba.com" "dream"

php bin/app.php /internauta/process-requests

## Dev tests

### Behat

docker exec -it muchacuba_api_php sh

cd domain

../vendor/bin/behat

## Backup & restore

# En el container mongo prod
docker exec -it muchacuba_api_mongo sh
apk add --no-cache mongodb-tools
mongodump --db muchacuba
rm -rf /data/db/dump
mv dump /data/db

# En mi pc
scp -r root@muchacuba.com:/root/muchacuba/var/db/dump/ /home/yosmanyga/Work/Projects/yosmy/muchacuba/code/var/db

# En el container mongo dev
apk add --no-cache mongodb-tools
mongorestore --drop --db muchacuba /data/db/dump/muchacuba
rm -rf /data/db/dump/muchacuba/*
