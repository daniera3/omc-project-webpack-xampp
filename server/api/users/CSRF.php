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

        $body = $request->getParsedBody() ?? [];
        $data = $request->getCookieParams();
        $session_id = (string)($data['PHPSESSID'] ?? "");
        if (session_status() === PHP_SESSION_NONE) {
            session_id($session_id);
            session_start();
        }
        if (isset($_SESSION['token'], $body['token']) && $body['token'] === $_SESSION['token']) {
//            $request = $request->withParsedBody(['CSRF_success' => true] + $body);
            return $next($request, $response);
        }
        return $response->withJson(["error" => "CSRF token not found", 'success' => false], 401);


    }

}