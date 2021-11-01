<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


/**
 * this function returns all user
 * @param Response $response
 * @param Request $request
 * @return Response
 */
function getAllUsers(Request $request, Response $response): Response
{
    $data = $request->getParsedBody();

    if (!(isset($data['success']) && $data['success'] === true)) {
        return $response->withJson(['error' => 'error'], 401);
    }
    $sql = "SELECT id, username, role FROM user  WHERE role != 'Sadmin'";

    try {
        $db = new db();
        $db = $db->connect();
        $statement = $db->query($sql);
        $user = [];
        while ($r = $statement->fetch()) {
            $data = [
                "id" => intval($r["id"]),
                "username" => strval($r["username"]),
                "role" => strval($r["role"])
            ];
            $user[] = $data;
        }
        $db = null;
    } catch (PDOException $e) {
        log_error('sql exception',$request, $e);
        return $response->withJson(['error' => 'sql failure'], 403);
    }

    return $response->withJson($user);
}

/**
 * this func return all derails of user
 * use hes cookie he sent for auth
 * @param Request $request
 * @param Response $response
 * @return Response
 */
function getUserDetail(Request $request, Response $response): Response
{
    $data = $request->getParsedBody();
    openSession($request);
    if (isset($_SESSION) && !empty($_SESSION)) {
        $user = [
            "id" => $_SESSION['id'],
            "username" => $_SESSION["username"],
            "email" => $_SESSION['email']

        ];

        return $response->withJson($user);
    }
    return $response->withJson(["error" => "user details not found"], 401);
}


/**
 * save in user session token for csrf
 * @param Request $request
 * @param Response $response
 * @return Response
 * @throws Exception
 */
function getAdmin(Request $request, Response $response): Response
{
    $data = $request->getParsedBody();
    if (isset($data['success']) && $data['success'] === true) {
        return $response->withJson($data);
    }
    return $response->withJson($data, 401);
}


/**
 * this function logs in user
 * @param Request $request
 * @param Response $response
 * @return Response
 * @throws JsonException
 */
function loginUser(Request $request, Response $response): Response
{
    $data = $request->getParsedBody();
    $username = (string)($data['username'] ?? "");
    $password = (string)($data['password'] ?? "");
    $user = array();
    if (find($username, $user) || empty($user)) {
        return notFoundResponse($response);
    }
    $user = json_decode(json_encode($user[0], JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
    if (password_verify(hash('sha512', $password), $user['password'])) {
        saveInSession($user);

        return $response->withJson(['success' => true]);
    }
    return $response->withJson(['error' => 'username/password is wrong'], 401);
}

/**
 * this function post new user
 * @param Request $request
 * @param Response $response
 * @return Response
 * @throws JsonException
 */
function postUser(Request $request, Response $response): Response
{
    $data = $request->getParsedBody();

    $data['password'] = password_hash(hash('sha512', $data['password']), PASSWORD_BCRYPT, ["cost" => 10]);
    insert($data);

    $user = array();
    if (find($data['username'], $user) || empty($user)) {
        return notFoundResponse($response);
    }

    $user = json_decode(json_encode($user[0], JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
    saveInSession($user);
    return $response->withJson(['success' => true], 201);
}

/**
 * this function updates user
 * @param Request $request
 * @param Response $response
 * @return Response
 */
function updateUser(Request $request, Response $response): Response
{

    $data = $request->getParsedBody();
    if (!isset($data['role']) && isset($data['role_login'])) {
        $data['role'] = $data['role_login'];
    }
    if (!update($data)) {
        return $response->withJson(["text" => "User not updated"], 405);
    }
    return $response->withJson(["text" => "User updated"]);
}

/**
 * this func delete datels in session
 * @param Request $request
 * @param Response $response
 * @return Response
 */
function logoutUser(Request $request, Response $response): Response
{
    openSession($request);
    $_SESSION = null;
    session_destroy();
    return $response->withJson(["success" => "logged out"]);
}

/**
 * fun crate new session for set-cookies to response and if func get user details save them in new session func crate
 * @param null $user
 * @return array
 */
function saveInSession($user = null): array
{
    $maxlifetime = 60 * 5; //30 min
    $secure = true; // only over HTTPS
    $httponly = true; // true : prevent JavaScript access to session cookie
    $samesite = 'None';
    session_set_cookie_params($maxlifetime, '/; samesite=' . $samesite, $_SERVER['HTTP_HOST'], $secure, $httponly);
    session_start();
    if ($user) {
        $_SESSION["id"] = $user['id'];
        $_SESSION["username"] = $user['username'];
        $_SESSION["email"] = $user['email'];
        $_SESSION["role"] = $user['role'];
    }
    return [
        "PHPSESSID" => session_id()
    ];
}

/**
 * with auth cookie we open user cookie if he for can take data from $_SESSION
 * func for not mack duplicates
 * @param Request $request
 * @param Response $response
 * @return Response
 */
function isSessionOpen(Request $request, Response $response): Response
{
    $data = $request->getCookieParams();
    // data['id'] = user id from cookie
    $session_id = (string)($data['PHPSESSID'] ?? "");
    if (session_status() === PHP_SESSION_NONE) {
        session_id($session_id);
        saveInSession();
    }
    if (isset($_SESSION) && !empty($_SESSION)) {
        return $response->withJson(['success' => true]);
    }
    return $response->withJson(['error' => 'session not found'], 401);
}

function openSession(Request $request)
{
    $data = $request->getCookieParams();
    // data['id'] = user id from cookie
    $session_id = (string)($data['PHPSESSID'] ?? "");
    if (session_status() === PHP_SESSION_NONE) {
        session_id($session_id);
        saveInSession();
    }
}

/**
 *
 * func make query from DB get user par username and return he's details in retData array
 * @param string $username
 * @param array $retData
 * @return int
 */
function find(string $username, array &$retData): int
{
    try {
        $statement = "
            SELECT
                id, username, password, email,role
            FROM
                user
            WHERE username = ? OR email = ?;
        ";


        $db = new db();
        $db = $db->connect();
        $statement = $db->prepare($statement);
        $statement->execute(array($username, $username));
        $db = null;
        while ($r = $statement->fetch()) {
            $data = [
                "id" => intval($r["id"]),
                "username" => strval($r["username"]),
                "password" => strval($r["password"]),
                "email" => strval($r["email"]),
                "role" => strval($r["role"])
            ];
            $retData[] = $data;
        }
        return 0;
    } catch (Throwable $t) {
        log_error('sql exception find in user',$t);
        return 1;
    }
}

/**
 *
 * func make query from DB get user par username and return how much row change
 * @param string $username
 * @param array $retData
 * @return int
 */
function insert(array $input): int
{
    try {
        $statement = "
            INSERT INTO user
                (username, password, email)
            VALUES
                (:username, :password, :email);
        ";


        $db = new db();
        $db = $db->connect();
        $statement = $db->prepare($statement);
        $statement->execute(array(
            'username' => $input['username'],
            'password' => $input['password'],
            'email' => $input['email'] ?? "",
        ));
        $db = null;
    } catch (Throwable $t) {
        log_error('sql exception insert in user',$t);
    }
    return $statement->rowCount();
}

/**
 *  func make query from DB get user details and update them in db return how much row change
 *
 * @param array $input
 * @return int
 */
function update(array $input): int
{


    if (($input['role_login'] === 'user' && $input['role'] === 'admin') ||
        ($input['role'] !== 'admin' && $input['role'] !== 'user' && $input['role_login'] !== 'Sadmin')
    ) {
        return false;
    }
    try {
        $statement = "
            UPDATE user
            SET
           
                role  = :role,
                email = :email
            WHERE id = :id;
        ";


        $db = new db();
        $db = $db->connect();
        $statement = $db->prepare($statement);
        $statement->execute(array(
            'id' => $input['id'],
            'email' => $input['email'] ?? '',
            'role' => $input['role'],
        ));
        $db = null;
    } catch (Throwable $t) {
        log_error('sql exception update in user',$t);
    }
    return $statement->rowCount();
}
