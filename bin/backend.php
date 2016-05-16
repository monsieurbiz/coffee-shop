<?php

require __DIR__ . '/../vendor/autoload.php';

use MonsieurBiz\CoffeeShop\Backend\Backend;

$backend = new Backend();
$backend->start();
