# Test

`cd test/docker`

`docker-compose up -d`

`docker exec -it geo_profile_php bash`

`composer install`

`cd test`

`../vendor/bin/behat`
