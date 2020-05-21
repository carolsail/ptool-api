<?php
/**
 * 请求响应json返回
 */
if(!function_exists('jsonReturn'))
{
	function ajaxReturn($data = '', $status = true)
	{
		return json(['status'=>$status, 'data'=>$data]);
	}
}

/**
 * 去首位空格并md5处理密码
 */
if(!function_exists('formatPassword'))
{
	function formatPassword($str='')
	{
		return md5(config('auth.encryption_key').trim($str));
	}
}

/**
 * 文件路径对应磁盘中真实位置
 */
if (!function_exists('diskPath')) {
    /**
     * 查看文件在磁盘中的具体路径
     *
     * @param  string  $path
     * @return string
     */
    function diskPath($path = '')
    {
        $path = \think\facade\Env::get('root_path') . ltrim($path, DIRECTORY_SEPARATOR);
        return $path;
    }
}

/**
 * public目录的文件路径
 */
if (!function_exists('publicPath')) {
    /**
     * public路径, 模板中静态资源引用
     * @param  string  $path
     * @return string
     */
    function publicPath($path = '')
    {
        //去index.php
        $path = '/' . ltrim($path, '/');
        return preg_replace("/\/(\w+)\.php$/i", '', request()->root()) . $path;
    }
}