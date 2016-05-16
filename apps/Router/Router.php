<?php

namespace MonsieurBiz\CoffeeShop\Router;

use Thruway\Peer\Router as BaseRouter;
use Thruway\Transport\RatchetTransportProvider;

class Router extends BaseRouter
{
    public function __construct(LoopInterface $loop = null)
    {
        parent::__construct($loop);
        $this->addTransportProvider(new RatchetTransportProvider("0.0.0.0", 9090));
    }
}
