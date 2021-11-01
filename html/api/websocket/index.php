<?php
require '../../../server/vendor/autoload.php';
require_once '../../../server/websocket/Chat.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\Chat;


$loop = React\EventLoop\Factory::create();

$webSock = new React\Socket\Server('127.0.0.1:8443', $loop);
$webSock = new React\Socket\SecureServer($webSock, $loop, [
    'local_cert'        => 'C:/xampp/apache/conf/ssl.crt/server.crt', // path to your cert
    'local_pk'          => 'C:/xampp/apache/conf/ssl.key/server.key', // path to your server private key
    'allow_self_signed' => FALSE, // Allow self signed certs (should be false in production)
    'verify_peer' => FALSE
]);

$webServer = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),8080
);

 $webServer->run();





$loop = React\EventLoop\Factory::create();

$webSock = new React\Socket\Server('127.0.0.1:8443', $loop);
$webSock = new React\Socket\SecureServer($webSock, $loop, [
    'local_cert'        => 'C:/xampp/apache/conf/ssl.crt/server.crt', // path to your cert
    'local_pk'          => 'C:/xampp/apache/conf/ssl.key/server.key', // path to your server private key
    'allow_self_signed' => FALSE, // Allow self signed certs (should be false in production)
    'verify_peer' => FALSE
]);

$webServer = new Ratchet\Server\IoServer(
    new Ratchet\Http\HttpServer(
        new Ratchet\WebSocket\WsServer(
            new Chat()
        )
    ),
    $webSock,
    $loop /** <--- here is the missing part */
);

 $webServer->run();


