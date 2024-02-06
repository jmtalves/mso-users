<?php

namespace Libraries;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Encrypt
{
    //@var string $jwt_secret jwt_secret
    private static $jwt_secret = "59gsv4Bn7VcEyM10uiZiyY72l2KJp31N";
    //@var string $secret_key secret_key
    private static $secret_key = "59gsv4Bn7VcEyM10uiZiyY72l2KJp31N";
    //@var string $secret_iv secret_iv
    private static $secret_iv = "1af0891441629cc190fe276bc7618841";
    //@var string $encrypt_method encrypt_method
    private static $encrypt_method = "AES-256-CBC";

    /**
     * encode
     *
     * @param  string $value
     * @return string
     */
    public static function encode(string $value)
    {
        $key = hash('sha256', self::$secret_key);
        $iv = substr(hash('sha256', self::$secret_iv), 0, 16);
        $output = openssl_encrypt($value, self::$encrypt_method, $key, 0, $iv);
        return base64_encode($output);
    }

    /**
     * encryptJwt
     *
     * @param  string $string string to encrypt
     * @throws \Exception
     * @return array
     */
    public static function encryptJwt($string)
    {
        $time = time();
        $exp = $time + 6000;
        $payload = [
            'sub' => $string,
            'iss' => $_SERVER['HTTP_HOST'] ?? 'local',
            'aud' => $_SERVER['HTTP_USER_AGENT'] ?? 'local',
            'iat' => $time,
            'exp' => $exp
        ];
        $headers = [];
        try {
            return ["token" => JWT::encode($payload, self::$jwt_secret, 'HS256', null, $headers), "expire" => $exp];
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }


    /**
     * decryptJwt
     *
     * @param  string $jwt jwt token to decrypt
     * @throws \Exception
     * @return array
     */
    public static function decryptJwt($jwt)
    {
        try {
            return  (array)JWT::decode($jwt, new Key(self::$jwt_secret, 'HS256'));
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
}
