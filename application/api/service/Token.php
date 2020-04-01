<?php
namespace app\api\service;

use \Firebase\JWT\JWT;

class Token{

	// 生产token
	public static function encoded($data)
    {
        $key = config('auth.token_salt');
        $exp = config('auth.token_expire_in');
        $body = [
            'iat' => time(),
            'exp' => time() + $exp,
            'data' => $data
        ];
        return JWT::encode($body, $key);
    }

    // 解析token
    public static function decoded()
    {
        $key = config('auth.token_salt');
        $token = request()->header('token');
        JWT::$leeway = 60;
        $decoded = JWT::decode($token, $key, ['HS256']);
        return (array)$decoded->data;
    }
}