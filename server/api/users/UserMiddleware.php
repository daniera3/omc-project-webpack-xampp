<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class UserMiddleware
{
    /**
     * validates middleware check if user sent all arg in request
     * @param Request $request
     * @param Response $response
     * @param $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $next): Response
    {
        $data = $request->getParsedBody();
        $data['username'] = htmlentities(strip_tags((string)($data['username'] ?? "")));
        $data['password'] = htmlentities(strip_tags((string)($data['password'] ?? "")));
        $request = $request->withParsedBody($data);
        return $next($request, $response);
    }
}
