<?php

namespace app\api\exception;

class GeneralException extends BaseException {
	public $code = 400;
    public $errorCode = 10000;
    public $msg = "通用异常错误提示";
}