# Dev

docker-compose -f docker/docker-compose.common.yml -f docker/docker-compose.dev.yml -p muchacuba up -d

# Prod

## First time

ssh root@muchacuba.com

git clone git@bitbucket.org:cubalider/muchacuba.git muchacuba

docker run -it --rm -v $(pwd)/app:/app -w /app --name node node:7.5.0-alpine sh
npm install

docker-compose -f docker/docker-compose.common.yml -f docker/docker-compose.prod.yml -p muchacuba up -d

cd api

composer install --optimize-autoloader

cp api/config/parameters.dist.yml api/config/parameters.yml`

chmod a+w api/var/cache

## Deploy

git pull --rebase

cd api

composer install --optimize-autoloader

rm -rf var/cache/*

chmod a+w var/cache

# Internauta

php bin/app.php internauta.process-requests
php bin/app.php internauta.importing.cubamessenger.import-users
php bin/app.php internauta.count-users
php bin/app.php internauta.insert-email

# Aloleiro

## Init

php bin/app.php aloleiro.create-admin-approval yosmanyga@gmail.com aloleiro_admin
php bin/app.php aloleiro.import-rates
php bin/app.php aloleiro.update-venezuelan-currency-exchange

https://localhost:3000/#/aloleiro/manage-businesses
Add business

https://localhost:3000/#/aloleiro/manage-approvals
Add approvals for 
yurijj2007@gmail.com
jefe_vnzwhyy_del_negocio@tfbnw.net
operador_hgvkurm_del_sistema@tfbnw.net

## Reset data
1. Drop database;
2. Open app in firefox and opera
3. Set fixtures
# firefox (owner) and opera (operator) user
php bin/app.php aloleiro.set-fixtures 1xurQe3HcVTzfZtgDXOcfe7phXJ2 6sfhOpoku9Pz1rPIHAsaBQ7CzKo1