<?php

/**
 * 状态码
 * 20000 表请求成功
 * 40000 表请求失败
 * 10010 表token过期
 * 10011 表token无效
 */
if( ! function_exists('ajaxReturn'))
{
	function ajaxReturn($data = '', $status = false, $code=0)
	{
		if(!$code){
			$code = $status ? 20000 : 40000;
		}
		return json(['status'=>$status, 'data'=>$data, 'code'=>$code]);
	}
}

if( ! function_exists('formatPassword'))
{
	function formatPassword($str='')
	{
		return md5(config('auth.encryption_key').trim($str));
	}
}