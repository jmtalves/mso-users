<?php

namespace Libraries;

use Libraries\Encrypt;
use Libraries\Response;

class Request
{
    /**
     * verifyToken
     *
     * @param  array $levels
     * @return array
     */
    public static function verifyToken(array $levels = [])
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION']) && !empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            Response::sendResponse(401, ["msg" => "Authentication not received"]);
        }
        $jwt = Encrypt::decryptJwt($_SERVER['HTTP_AUTHORIZATION']);
        if (!empty($jwt['error'])) {
            Response::sendResponse(503, ["msg" => $jwt['error']]);
        }
        list($apikey, $user_id, $type, $auth) = array_map('trim', explode('-', $jwt['sub']));
        if (!in_array($type, $levels)) {
            Response::sendResponse(405, ["msg" => "Method Not Allowed"]);
        }
        return ["api_key" => $apikey, "user_id" => $user_id, "type" => $type, "auth" => $auth];
    }


    /**
     * getPostParams
     *
     * @return array
     */
    public static function getPostParams()
    {
        if (empty($_POST)) {
            $json = file_get_contents('php://input');
            return json_decode($json, true);
        } else {
            return $_POST;
        }
    }
}
