<?php
namespace app\api\validate;

class Task extends Base
{
    protected $rule =   [
        'title'  => 'require'
    ];
}