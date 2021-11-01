<?php


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class CSRF
{
    /**
     *
     * auth user open he's session and check he's CSRF he sent in body of request and save in he's session if them equal
     * @param Request $request
     * @param Response $response
     * @param $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $next): Response
    {

        $body = $request->getHeader('CSRF');

        openSession($request);
        //return $next($request, $response);/// need delete this row
        if (isset($_SESSION['token'])&&  !empty($body) && $body[0] === $_SESSION['token']) {
            return $next($request, $response);
        }
        return $response->withJson(["error" => "CSRF token not found", 'success' => false], 401);


    }

}