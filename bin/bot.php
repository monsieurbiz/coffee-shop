<?php

require_once __DIR__ . '/../vendor/autoload.php';

$bot = new MonsieurBiz\CoffeeShop\Bot\Bot();
$bot->setToken(file_get_contents(__DIR__ . '/../TOKEN'));
$bot
    ->initCommands()
    ->run(false, false)
;
