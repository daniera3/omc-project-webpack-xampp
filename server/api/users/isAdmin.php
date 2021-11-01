<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class isAdmin
{
    /**
     * Admin middleware
     * check if user sent request he's premiss of admin if he can make request
     * @param Request $request
     * @param Response $response
     * @param $next
     * @return mixed
     */
    public function __invoke(Request $request, Response $response, $next): Response
    {
        $body = $request->getParsedBody() ?? [];
        openSession($request);
        if (isset($_SESSION['role']) && in_array($_SESSION['role'], ["admin", "Sadmin"])) {
            $request = $request->withParsedBody(['success' => true] + $body);
            return $next($request, $response);
        }
        return $response->withJson(["error" => "Unauthorized", 'success' => false], 401);


    }

}