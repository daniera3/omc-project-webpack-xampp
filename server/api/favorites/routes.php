<?php
require_once 'favorites.php';
require_once 'AuthMiddleware.php';
require_once 'getCSRF.php';
require_once 'CSRF.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * routes to favorite and after this specific uri
 *
 * ex /favorites/getAllPerUser
 *   /favorites/*
 */
$app->group('/favorites', function () {
    $this->get('/getAllPerUser', function (Request $req, Response $res): Response {
        return getFavorite($req, $res);
    });
    $this->put('/', function (Request $req, Response $res): Response {
        return updateFavorite($req, $res);
    });

})->add(new AuthMiddleware())->add(new CSRF());