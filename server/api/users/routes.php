<?php

require_once 'users.php';
require_once 'UserMiddleware.php';
require_once 'isAdmin.php';




use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * all routs under user uri
 */
$app->group('/user', function () {
    $this->group("",function (){
        $this->post('/login', function (Request $req, Response $res): Response {
            return loginUser($req, $res);
        })->add(new UserMiddleware());
        $this->post('/logout', function (Request $req, Response $res): Response {
            return logoutUser($req, $res);
        })->add(new UserMiddleware());
        $this->get('/session', function (Request $req, Response $res): Response {
            return isSessionOpen($req, $res);
        });
    })->add(new getCSRF());
    $this->post('/getAllUsers', function (Request $req, Response $res): Response {
        return getAllUsers($req, $res);
    })->add(new isAdmin())->add(new CSRF());
    $this->get('/details', function (Request $req, Response $res): Response {
        return getUserDetail($req, $res);
    })->add(new AuthMiddleware())->add(new CSRF());
    $this->post('/register', function (Request $req, Response $res): Response {
        return postUser($req, $res);
    })->add(new getCSRF())->add(new UserMiddleware());
    $this->put('/updateRole', function (Request $req, Response $res): Response {
        return updateUser($req, $res);
    })->add(new AuthMiddleware())->add(new isAdmin())->add(new CSRF());
    $this->put('/update', function (Request $req, Response $res): Response {
        return updateUser($req, $res);
    })->add(new AuthMiddleware())->add(new CSRF());
    $this->post('/isAdmin', function (Request $req, Response $res): Response {
        return getAdmin($req, $res);
    })->add(new isAdmin());
    
});
