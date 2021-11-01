<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require_once (__DIR__.'/../src/config/initialize.php');

class Chat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {

        $this->clients = new \SplObjectStorage;

    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $msg=json_decode($msg,true);
        if(isset($msg['info']) && !empty($msg['info']))
        {
            $this->clients->detach($from);
            $data=['client' => $from, "info" => $msg['info']];
            $this->clients->attach((object)$data);

        }
        else {
            foreach ($this->clients as $client) {
                if (isset($client->client) && $from !== $client->client && $client->info['id'] === $msg['id']) {
                    // The sender is not the receiver,  send to each client connected
                    $client->client->send(json_encode(['massage'=>$msg['massage'],'sender'=>$msg['sender']]));
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}<br/>";

        $conn->close();
    }
}