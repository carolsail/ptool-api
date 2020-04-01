<?php
namespace app\api\controller;

use app\api\service\Token;
use Random;

class Auth extends Base
{
    /**
     * 登陆操作
     * 返回token
     */
    public function login() {
    	$data = input('post.');
    	if(!(isset($data['account']) && isset($data['password']))) return ajaxReturn('参数有误');

    	$row = model('Account')->check(request()->only(['account', 'password']));
    	if(!$row) return ajaxReturn('账号或密码有误');

    	// 数据库中加入局部盐salt
        $salt = Random::alnum(6);
        $row->salt = $salt;
        $row->save();

        $data = [
            'id' => $row->id,
            'account' => $row->account,
            'nickname' => $row->nickname,
            'email' => $row->email,
            'phone' => $row->phone,
            'scope' => $row->scope,
            'salt' => $salt
        ];
        $token = Token::encoded($data);

        return ajaxReturn($token, true);
    }

    /**
     * 验证操作
     * header携带token参数
     * 返回用户信息
     */
    public function verify() {
        try{
            $data = Token::decoded();
            $row = model('Account')->checkSalt(['id'=>$data['id'], 'salt'=>$data['salt']]);
            if(!$row) return ajaxReturn('局部盐已变动，token刷新', false, 10011);
            return ajaxReturn($data, true);
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            // 签名不正确
            return ajaxReturn($e->getMessage(), false, 10011);
        } catch (\Firebase\JWT\BeforeValidException $e) {
            // 签名在某个时间点之后才能用
            return ajaxReturn($e->getMessage(), false, 10011);
        } catch (\Firebase\JWT\ExpiredException $e) {
            // token过期
            return ajaxReturn($e->getMessage(), false, 10010);
        } catch (\Exception $e) {
            // 其他错误
            return ajaxReturn($e->getMessage(), false, 10011);
        }
    }

    /**
     * 更新token
     * header携带token参数
     */
    public function refresh() {
        $data = Token::decoded();
        $row = model('Account')->getRowById($data['id']);
        // 刷新局部盐
        $salt = Random::alnum(6);
        $row->salt = $salt;
        $row->save();
        $data = [
            'id' => $row->id,
            'account' => $row->account,
            'nickname' => $row->nickname,
            'email' => $row->email,
            'phone' => $row->phone,
            'scope' => $row->scope,
            'salt' => $salt
        ];
        $token = Token::encoded($data);
        return ajaxReturn($token, true);
    }
}
