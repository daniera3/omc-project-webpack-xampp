<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class AuthMiddleware
{
    /**
     * Auth middleware before start work on request this middleware check if request he's cookie for
     * auth and convert hem to user data
     *
     * @param Request $request
     * @param Response $response
     * @param  $next
     * @return Response
     *
     */
    public function __invoke(Request $request, Response $response, $next): Response
    {
        $parsedBody = $request->getParsedBody();
        openSession($request);
        if (isset($_SESSION['id'])) {
            $parsedBody["id_login"] = $_SESSION['id'];
            $parsedBody["role_login"] = $_SESSION['role'];
            $request = $request->withParsedBody($parsedBody);
            return $next($request, $response);
        }
        return $response->withJson(['error' => 'Unauthorized'], 401);
    }
}


