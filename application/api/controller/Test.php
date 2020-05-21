<?php
namespace app\api\controller;
use app\api\exception\TokenException;

class Test extends Base
{
	public function index(){
		throw new TokenException(['errorCode'=>0, 'msg'=>'123', 'code'=>200]);
	}
}