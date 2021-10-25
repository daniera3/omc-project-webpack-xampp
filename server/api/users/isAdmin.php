<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class isAdmin
{
    public function __invoke(Request $request, Response $response, $next)
    {
        $body = $request->getParsedBody() ?? [];
        $data = $request->getCookieParams();
        $session_id = (string)($data['PHPSESSID'] ?? "");

        if (session_status() === PHP_SESSION_NONE) {
            session_id($session_id);
            session_start();

        }

        if (isset($_SESSION['role']) && in_array($_SESSION['role'], ["admin","Sadmin"])) {
            $request = $request->withParsedBody(['success' => true] + $body);
            return $next($request, $response);
        }
        return $response->withJson(["error" => "Unauthorized", 'success' => false], 401);


    }

}