<?php


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class getCSRF
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
        $response=$next($request, $response);
        openSession($request);
        if (isset($_SESSION) && !empty($_SESSION)) {
            $_SESSION['token'] = bin2hex(random_bytes(50));
            $body = json_decode($response->getBody(), true);
            //$body = $request->getParsedBody();
            $body['token'] =  $_SESSION['token'];
            $response = $response->withJson($body, $response->getStatusCode());
        }
        return $response ;
    }
}
