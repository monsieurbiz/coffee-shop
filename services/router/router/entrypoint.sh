#!/bin/bash

cd /var/www/coffee-shop/ && \
composer install --no-interaction

php -f /var/www/coffee-shop/bin/router.php

