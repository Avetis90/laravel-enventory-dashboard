#!/bin/sh

# DB setup
DB_NAME=smartecom
DB_USER=root
DB_PASS=
mysql -u root -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8 COLLATE utf8_general_ci;"
mysql -u root -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -u root -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'%' IDENTIFIED BY '${DB_PASS}';"

# Configure .env
cp /home/vagrant/htdocs/.env.example /home/vagrant/htdocs/.env

# Generate params
cd ~/htdocs
php artisan key:generate
