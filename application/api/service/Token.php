<?php
namespace app\api\service;

use \Firebase\JWT\JWT;
use app\api\exception\TokenException;

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

    // 检验token有效性
    public static function verify()
    {
        try{
            $data = self::decoded();
            $row = model('Account')->checkSalt(['id'=>$data['id'], 'salt'=>$data['salt']]);
            if(!$row){
                throw new TokenException(['errorCode'=>10011, 'msg'=>'局部盐已变动，token刷新']);
            }
            return $data;
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            // 签名不正确
            throw new TokenException(['errorCode'=>10011, 'msg'=>$e->getMessage()]);
        } catch (\Firebase\JWT\BeforeValidException $e) {
            // 签名在某个时间点之后才能用
            throw new TokenException(['errorCode'=>10011, 'msg'=>$e->getMessage()]);
        } catch (\Firebase\JWT\ExpiredException $e) {
            // token过期
            throw new TokenException(['errorCode'=>10010, 'msg'=>$e->getMessage()]);
        }
    }
}