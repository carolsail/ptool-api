<?php
namespace app\api\controller;

use app\api\service\Token;
use Random;
use app\api\exception\GeneralException; 

class Auth extends Base
{
    /**
     * 登陆操作
     * 返回token
     */
    public function login() {
    	$data = input('post.');
    	if(!(isset($data['account']) && isset($data['password']))){
            throw new GeneralException(['msg'=>'账号或密码有误']);
        }

    	$row = model('Account')->check(request()->only(['account', 'password']));
    	if(!$row){
            throw new GeneralException(['msg'=>'账号或密码有误']);
        }

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

        return ajaxReturn($token);
    }

    /**
     * 验证操作
     * header携带token参数
     * 返回用户信息
     */
    public function info() {
        $info = Token::verify();
        return ajaxReturn($info);
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
        return ajaxReturn($token);
    }
}
