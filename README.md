# Dev init

docker run -it --rm -v $(pwd)/app:/app -w /app --name node node:7.5.0-alpine sh
npm install

docker-compose \
-f docker/docker-compose.common.yml \
-f docker/docker-compose.dev.yml \
-p muchacuba \
up -d

## Api

cd api

composer install

cp api/config/parameters.dist.yml api/config/parameters.yml

chmod a+w api/var/cache

## Dev tests

### Behat

docker exec -it muchacuba_php sh

cd domain

../vendor/bin/behat
