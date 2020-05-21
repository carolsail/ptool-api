<?php
namespace app\api\validate;

use think\Validate;
use app\api\exception\GeneralException;

class BaseValidate extends Validate {
	
	public function goCheck($data = []){
		$param = array_key_exists('param', $data) ? $data['param'] : input('param.');
		$validate = array_key_exists('scene', $data) ? $this->scene($data['scene']) : $this;
		
		if(!$validate->check($param)){
			$error_msg = is_array($this->error) ? implode(';', $this->error) : $this->error;
			throw new GeneralException(['msg' => $error_msg]);
		}
		return true;
	}

	// 大于0整数
	public function isInt($value, $rule='', $data='', $field=''){
		if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
		    return true;
		}
		return false;
	}

	// 非空
	public function isNotEmpty($value, $rule='', $data='', $field=''){
		if (empty($value)) {
            return false;
        }
        return true;
	}

	// 手机号
	public function isMobile($value, $rule='', $data='', $field=''){
		$rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
	}
}