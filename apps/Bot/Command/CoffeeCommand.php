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

    protected function configure()
    {
        // Nothing here
    }

    protected function execute($message, $context)
    {
        // Only DM
        if (!isset($message['channel'], $message['text']) || !preg_match('`^D.+$`', $message['channel'])) {
            return;
        }
        $this->setChannel($message['channel']);

        # Help
        $regex = '`^\s*help\s*$`';
        if (preg_match($regex, $message['text'])) {
            $help = <<<HELP
So… you need help, isn't it?

I can only do the following tasks depending on what you want:

— Write `list` and I'll give you the current list.
— Write `add item [quantity]` with `item` as the item's number that you want to add to the list, and `quantity` as the number of items you want to add (by default it's 1). 

Happy day!

HELP;
            $this->send($this->getCurrentChannel(), null, $help);
            return;
        }

        # List
        $regex = '`^\s*list`';
        if (preg_match($regex, $message['text'])) {
            $this->_processList();
            return;
        }

        # Add
        $regex = '`^\s*add\s*([0-9]+)\s*([0-9]*)$`';
        $matches = [];
        if (preg_match($regex, $message['text'], $matches)) {
            $this->_processAdd($matches);
            return;
        }
    }

    protected function _processAdd(array $matches)
    {
        $db = $this->_openDb();

        // Get current list
        $stmt = $db->prepare("SELECT * FROM list WHERE active = 1;");
        $result = $stmt->execute();
        $list = $result->fetchArray(SQLITE3_ASSOC);

        // Get item if it exists
        $stmt = $db->prepare("SELECT * FROM list_item WHERE list_id = {$list['list_id']} AND item_id = {$matches[1]}");
        $result = $stmt->execute();
        $element = $result->fetchArray(SQLITE3_ASSOC);

        if (empty($element)) {
            $db->exec(sprintf("INSERT INTO list_item (item_id, list_id, qty) VALUES (%d, %d, %d)", $matches[1], $list['list_id'], (isset($matches[2]) && $matches[2] ? (int) $matches[2] : 1)));
        } else {
            $newQty = (int) $element['qty'] + (isset($matches[2]) ? (int) $matches[2] : 1);
            $db->exec(sprintf("UPDATE list_item SET qty = $newQty WHERE item_id = %d AND list_id = %d", $matches[1], $list['list_id'], 1));
        }

        $db->close();

        $this->send($this->getCurrentChannel(), null, "C'est fait !");
    }

    protected function _processList()
    {
        $db = $this->_openDb();

        // Get current list
        $stmt = $db->prepare("SELECT * FROM list WHERE active = 1;");
        $result = $stmt->execute();
        $list = $result->fetchArray(SQLITE3_ASSOC);

        // Get items of current list
        $sql = <<<SQL
SELECT list_item.item_id, list_item.qty FROM list_item
INNER JOIN list ON list.list_id = list_item.list_id
WHERE list.active = 1;
SQL;
        $stmt = $db->prepare($sql);
        $result = $stmt->execute();
        $listItems = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $listItems[$row['item_id']] = (int) $row['qty'];
        }


        // Get items
        $stmt = $db->prepare('SELECT * FROM item;');
        $result = $stmt->execute();

        $itemsToReturn = ["Voici les produits disponibles :"];
        while ($row = $result->fetchArray(SQLITE3_ASSOC))
        {
            $qty = isset($listItems[$row['item_id']]) ? sprintf(" (dont *%d* en attente de commande)", $listItems[$row['item_id']]) : '';
            $itemsToReturn[] = sprintf("[`%d`] *%s*", $row['item_id'], $row['name']) . $qty;
        }

        $this->send($this->getCurrentChannel(), null, implode("\n", $itemsToReturn));
        $db->close();
    }

    protected function _openDb()
    {
        return new \SQLite3(__DIR__ . '/../../../db/coffee.sqlite');
    }
}
