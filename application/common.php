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

if (! function_exists('path_disk')) {
    /**
     * 查看文件在磁盘中的具体路径
     *
     * @param  string  $path
     * @return string
     */
    function path_disk($path = '')
    {
        $path = \think\facade\Env::get('root_path') . ltrim($path, DIRECTORY_SEPARATOR);
        return $path;
    }
}

if (! function_exists('public_path')) {
    /**
     * public路径, 模板中静态资源引用
     * @param  string  $path
     * @return string
     */
    function public_path($path = '')
    {
        //去index.php
        $path = '/' . ltrim($path, '/');
        return preg_replace("/\/(\w+)\.php$/i", '', request()->root()) . $path;
    }
}