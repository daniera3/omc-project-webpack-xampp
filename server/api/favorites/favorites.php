<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


/**
 * return response with all favorites list par user from DB
 * @throws JsonException
 */
function getFavorite(Request $request, Response $response): Response
{
    $input = $request->getParsedBody();

    $result = array();
    if (findFavorites($input['id_login'], $result) || empty($result)) {
        return notFoundResponse($response);
    }


    return $response->withJson(['favorites' => json_decode(json_encode($result[0], JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR)["favorites"]], 200);
}



/**
 * save in DB new favorites list of user
 *
 * @throws JsonException
 */
function updateFavorite(Request $request, Response $response): Response
{
    $input = $request->getParsedBody();

    if (!isset($input['favorites'])) {
        return $response->withJson(['error' => 'Invalid input'], 422);
    }
    //    $input['favorites'] = htmlentities(strip_tags((string)(json_encode($input['favorites'])))); // add after qa
    $result = [];
    if (findFavorites($input['id_login'], $result) || empty($result)) {
        return notFoundResponse($response);
    }

    if (favoriteUpdate($input)) {
        $client = new WebSocket\Client("ws://localhost:8080");
        $client->text(json_encode(['massage'=>'updata','id'=>$input['id_login'],'sender'=>$input['sender']]));
        $client->close();
    }
    return $response->withStatus(201);
}

/**
 * return response with code 404
 * @param Response $response
 * @return Response
 */
function notFoundResponse(Response $response): Response
{
    return $response->withJson("Not Found Response", 404);
}

/**
 * func make query from DB get favorite list of user and save result in param $retData
 *
 * @param int $userId
 * @param array $retData
 * @return int
 */
function findFavorites(int $userId, array &$retData): int
{
    try {

        $statement = "
            SELECT
                id,user_id,favorites
            FROM
                favorite
            WHERE user_id = ? ;
        ";


        $db = new db();
        $db = $db->connect();
        $statement = $db->prepare($statement);
        $statement->execute(array($userId));
        $db = null;
        while ($r = $statement->fetch()) {
            $data = [
                "id" => intval($r["id"]),
                "user_id" => intval($r["user_id"]),
                "favorites" => strval($r["favorites"])
            ];
            $retData[] = $data;
        }
        return 0;
    } catch (Throwable $t) {
        log_error('sql exception findFavorites in favorites', $t);
        return 1;
    }
}

/**
 * func make query from DB get favorite list of user and return how much row change
 * @param array $input
 * @return int
 * @throws JsonException
 */
function favoriteUpdate(array $input): int
{
    try {
        $statement = "
            UPDATE favorite
            SET
                favorites = :favorites
            WHERE user_id = :user_id;
        ";


        $db = new db();
        $db = $db->connect();
        $statement = $db->prepare($statement);
        $statement->execute(array(
            'user_id' => $input['id_login'],
            'favorites' => json_encode($input['favorites'], JSON_THROW_ON_ERROR),
        ));
        $db = null;
    } catch (Throwable $t) {
        log_error('sql exception favoriteUpdate in favorites', $t);
    }
    return $statement->rowCount();
}



