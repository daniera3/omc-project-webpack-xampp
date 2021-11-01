<?php

use Slim\App;
use Slim\Container;
use Slim\Exception\MethodNotAllowedException;
use Slim\Exception\NotFoundException;

require '../../server/vendor/autoload.php';
require '../../server/src/config/initialize.php';


$c = new Container();
$c['errorHandler'] = static function ($c) {
    return static function ($request, $response, $exception) use ($c) {
        log_error('exception:', $exception);
        return $response->withStatus(500)
            ->withHeader('Content-Type', ' *')
            ->write(json_encode($exception, JSON_THROW_ON_ERROR));
    };
};
$c['phpErrorHandler'] = static function ($c) {
    return static function ($request, $response, $exception) use ($c) {
        log_error('exception:', $exception);
        return $response->withStatus(500)->withJson($exception);
    };
};
$app = new App($c);

$app->add(function ($req, $res, $next) {

    log_info('request:',
        $req->getUri()->getPath(),
        $req->getMethod(),
//        $req->getHeaders(),
        $req->getCookieParams(),
//        $req->getUploadedFiles(),
        !empty($req->getParsedBody()) ? array_keys($req->getParsedBody()): null
    );

    log_request('',
        $req->getUri()->getPath(),
        $req->getMethod(),
//        $req->getHeaders(),
        $req->getCookieParams(),
//        $req->getUploadedFiles(),
        !empty($req->getParsedBody()) ? array_keys($req->getParsedBody()): null
    );

    $response = $next($req, $res);
    log_info('response:',  $response->getStatusCode(),$response->getReasonPhrase(),json_decode($response->getBody(),true));
    if ((int)($response->getStatusCode() / 100) === 2) {
        log_response_pass('',$response->getStatusCode(),$response->getReasonPhrase(), $response->getBody());
    } else {
        log_response_fail('', $response->getStatusCode(),$response->getReasonPhrase());
    }
    return $response
        // ->withHeader('Access-Control-Allow-Origin', 'http://localhost:3000')
        // ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        // ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
        // ->withHeader('Access-Control-Allow-Credentials', ' true')
        ->withHeader('Content-Type', ' *')
        ->withHeader('Access-Control-Max-Age', ' 3600')
        ->withHeader('X-XSS-Protection', ' 1; mode=block')
        ->withHeader('X-Frame-Options', ' timeorigin')
        ->withHeader('X-Content-Type-Options', ' sniff')
        ->withHeader('Strict-Transport-Security', ' max-age=31536000')
        ->withHeader("Content-Security-Policy", " default-src 'none'; script-src 'self'; connect-src 'self'; img-src 'self'; style-src 'self';")
        ->withHeader('Referrer-Policy', ' no-referrer')
        ->withHeader('Expect-CT', ' max-age=7776000, enforce');
});

// Routes
require '../../server/api/favorites/routes.php';
require '../../server/api/users/routes.php';


try {
    $app->run();
} catch (MethodNotAllowedException | NotFoundException | Exception $e) {
    log_error("API failure", $e);

}

