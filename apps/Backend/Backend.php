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

        $this->_db = new \SQLite3($dbDir . '/coffee.sqlite');

        $result = $this->_db->query("SELECT name FROM sqlite_master WHERE type='item'");

        if ($result->fetchArray() === false) {
            $this->installDb();
        }
    }

    public function installDb()
    {
        // Lists
        $time = time();
        $this->_db->query("CREATE TABLE list (list_id INTEGER PRIMARY KEY AUTOINCREMENT, who VARCHAR(30), created_at INTEGER, shipping_at INTEGER, active INTEGER(1))");
        $this->_db->query("INSERT INTO list (who, created_at, active) VALUES ('Monsieur Biz', $time, 1);");

        // List items
        $this->_db->query("CREATE TABLE list_item (item_id INTEGER, list_id INTEGER, qty INTEGER)");

        // Items
        $this->_db->query("CREATE TABLE item (item_id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(120), image VARCHAR(255))");

        $items = [
            # Intenso
            ["Kazaar", "/ecom/medias/sys_master/public/9375751012382/C-0089-small-60x60.png", 12],
            ["Dharkan", "/ecom/medias/sys_master/public/9375751602206/C-0090-small-60x60.png", 11],
            ["Ristretto", "/ecom/medias/sys_master/public/9375746555934/C-0023-small-60x60.png", 10],
            ["Arpeggio", "/ecom/medias/sys_master/public/9375741706270/C-0001-small-60x60.png", 9],
            ["Roma", "/ecom/medias/sys_master/public/9375747014686/C-0026-small-60x60.png", 8],

            # Espresso
            ["Livanto", "/ecom/medias/sys_master/public/9375746097182/C-0017-small-60x60.png", 6],
            ["Capriccio", "/ecom/medias/sys_master/public/9375746097182/C-0017-small-60x60.png", 5],
            ["Volluto", "/ecom/medias/sys_master/public/9375748522014/C-0039-small-60x60.png", 4],
            ["Cosi", "/ecom/medias/sys_master/public/9740977668126/Cosi-small-60x60.png", 4],

            # Pure origin
            ["Indriya from India", "/ecom/medias/sys_master/public/9375745572894/C-0015-small-60x60.png", 10],
            ["Rosabaya de Colombia", "/ecom/medias/sys_master/public/9375747538974/C-0027-small-60x60.png", 6],
            ["Dulsão do Brasil", "/ecom/medias/sys_master/public/9375744655390/C-0009-small-60x60.png", 4],
            ["Bukeela ka Ethiopia", "/ecom/medias/sys_master/public/9375752126494/C-0103-small-60x60.png", 3],

            # Lungo
            ["Fortissio Lungo", "/ecom/medias/sys_master/public/9381719441438/C-0126-small-60x60.png", 8],
            ["Vivalto Lungo", "/ecom/medias/sys_master/public/9375748063262/C-0038-small-60x60.png", 4],
            ["Linizio Lungo", "/ecom/medias/sys_master/public/9375749046302/C-0057-small-60x60.png", 4],

            # Dacaffeinato
            ["Arpeggio Decaffeinato", "/ecom/medias/sys_master/public/9559895605278/ArpeggioDecaffeinato-small-60x60.png", 9],
            ["Volluto Decaffeinato", "/ecom/medias/sys_master/public/9559893999646/VollutoDecaffeinato-small-60x60.png", 4],
            ["Vivalto Lungo Decaffeinato", "/ecom/medias/sys_master/public/9559900717086/VivaltoDecaffeinato-small-60x60.png", 4],
        ];

        foreach ($items as $key => $item) {
            list($name, $uri) = $item;
            $key++;
            $this->_db->query("INSERT INTO item (name, image) VALUES ('$name', 'https://www.nespresso.com$uri');");
        }
    }

    public function onEvent($args)
    {
        $data = $args[1];

        if (isset($data->action)) {
            switch ($data->action) {
                case 'list':
                    $this->doList($data);
                    break;
            }
        }
    }

    public function doList($data)
    {
        // send to web hook
        var_dump([
            0 => ['name' => 'voluto'],
            1 => ['name' => 'grand mère'],
            2 => ['name' => 'bla'],
        ]);
    }
}
