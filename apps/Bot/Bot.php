<?php

namespace MonsieurBiz\CoffeeShop\Bot;

use PhpSlackBot\Bot as BaseBot;

class Bot extends BaseBot
{

    /**
     * Init commands list
     * @return $this
     * @throws \Exception
     */
    public function initCommands()
    {
        $this->loadCommand(new Command\CoffeeCommand());

        return $this;
    }

}
