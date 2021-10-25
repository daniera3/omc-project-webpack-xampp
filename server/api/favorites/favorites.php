<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


/**
 * @throws JsonException
 */
function getFavorite(Request $request, Response $response): Response
{
    $input = $request->getParsedBody();

    $result = array();
    if (findFavorites($input['id_login'], $result)) {
        return notFoundResponse($response);
    }


    return $response->withJson(json_decode(json_encode($result[0], JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR)["favorites"]);
}


/**
 * @throws JsonException
 */
function updateFavorite(Request $request, Response $response): Response
{
    $input = $request->getParsedBody();

    if (!isset($input['favorites'])) {
        return $response->withJson(['error' => 'Invalid input'], 422);
    }
//    $input['favorites'] = htmlentities(strip_tags((string)(json_encode($input['favorites'])))); // add after qa
    $result =[] ;
    if (findFavorites($input['id_login'],$result)) {
        return notFoundResponse($response);
    }

    favoriteUpdate($input);
    return $response->withStatus(201);
}


function notFoundResponse($response)
{
    return $response->withJson("Not Found Response", 404);
}


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
        return 1;
    }

}

/**
 * @param array $input
 * @return int
 * @throws JsonException
 */
function favoriteUpdate(array $input): int
{
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
    return $statement->rowCount();

}