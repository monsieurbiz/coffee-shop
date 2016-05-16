<?php

namespace MonsieurBiz\CoffeeShop\Bot\Command;

use PhpSlackBot\Command\BaseCommand;
use Thruway\ClientSession;
use Thruway\Connection;
use Thruway\Message\Message;

// Coffee command
class CoffeeCommand extends BaseCommand
{
    protected $_connection;
    public $session;

    protected function configure()
    {
        $this->setName('!cafe');


    }

    protected function execute($message, $context)
    {
        # List
        $regex = '`^\s*!cafe\s*list`';
        $matches = [];
        if (preg_match($regex, $message['text'], $matches)) {
            var_dump("Coucou");

            $this->_connection = new Connection(
                [
                    "realm" => 'coffee-realm',
                    "url" => 'ws://127.0.0.1:9090',
                ]
            );
            $this->_connection->on(
                'open',
                function (ClientSession $session) {
                    $session
                        ->publish('com.monsieurbiz.coffee.bot', [Message::MSG_PUBLISH, ["action" => "list"]], [],
                            ["acknowledge" => true])
                        ->then(
                            function () {
                                echo "Publish Acknowledged!\n";
                            },
                            function ($error) {
                                // publish failed
                                echo "Publish Error {$error}\n";
                            }
                        )
                    ;
                }
            );
            $this->_connection->open();
            $this->_connection->close();
        }
//        $this->send($this->getCurrentChannel(), null, 'Hello !');
    }

}
