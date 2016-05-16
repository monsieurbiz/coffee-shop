<?php

namespace MonsieurBiz\CoffeeShop\Backend;

use Thruway\ClientSession;
use Thruway\Connection;

class Backend
{
    /**
     * @var Connection
     */
    private $_connection;

    /**
     * @var SQLite3
     */
    private $_db;

    public function __construct()
    {
        $this->_connection = new Connection(
            [
                "realm" => 'coffee-realm',
                "url" => 'ws://127.0.0.1:9090',
            ]
        );

        $this->_connection->on(
            'open',
            function (ClientSession $session) {
                $session->subscribe('com.monsieurbiz.coffee.bot', [$this, 'onEvent']);
            }
        );

        $this->initDb();
    }

    public function start()
    {
        $this->_connection->open();
    }

    public function initDb()
    {
        $dbDir = __DIR__ . '/../../db';

        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755);
        }

        $this->_db = new \SQLite3($dbDir . '/coffee.db');

        $result = $this->_db->query("SELECT name FROM sqlite_master WHERE type='item'");

        if ($result->fetchArray() === false) {
            $this->installDb();
        }
    }

    public function installDb()
    {
        //
    }

    public function onEvent($args)
    {
        echo "on event:\n";
        var_dump($args);
    }

}
