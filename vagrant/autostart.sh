#!/bin/sh

cd ~/htdocs
composer install
composer dump-autoload

php artisan migrate
