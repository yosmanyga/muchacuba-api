# Dev

docker-compose -f docker/docker-compose.common.yml -f docker/docker-compose.dev.yml -p muchacuba up -d

# Prod

ssh root@muchacuba.com

git clone git@bitbucket.org:cubalider/muchacuba.git muchacuba

docker run -it --rm -v $(pwd)/app:/app -w /app --name node node:7.5.0-alpine sh
npm install

docker-compose -f docker/docker-compose.common.yml -f docker/docker-compose.prod.yml -p muchacuba up -d

cd api

composer install --optimize-autoloader

cp api/config/parameters.dist.yml api/config/parameters.yml`

chmod a+w api/var/cache

# Internauta

php bin/app.php internauta.process-requests
php bin/app.php internauta.importing.cubamessenger.import-users
php bin/app.php internauta.count-users
php bin/app.php internauta.insert-email

# Aloleiro

php bin/app.php aloleiro.import-countries
php bin/app.php aloleiro.import-rates
php bin/app.php aloleiro.create-business 15 0.0

## Reset data
1. Drop database;
2. Open app in firefox and opera
3. Set fixtures
# firefox (owner) and opera (operator) user
php bin/app.php aloleiro.set-fixtures 1xurQe3HcVTzfZtgDXOcfe7phXJ2 6sfhOpoku9Pz1rPIHAsaBQ7CzKo1
4. Admin
# Yosmany (admin)
php bin/app.php aloleiro.promote-user KnqmEoxOhxNaeB9BSnTEMV8zU1H2 58f5749a4ee2f aloleiro_admin