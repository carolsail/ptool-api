<?php
namespace app\api\model;

class Account extends Base
{

	public function check($data) {
		return Self::where(['account' => $data['account'], 'password' => formatPassword($data['password'])])->find();
	}

	public function checkSalt($data) {
		return Self::where(['id' => $data['id'], 'salt' => $data['salt']])->find();
	}

	public function getRowById($id) {
		return Self::find($id);
	}
}