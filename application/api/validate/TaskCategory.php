<?php
namespace app\api\validate;

class TaskCategory extends Base
{
    protected $rule =   [
        'title'  => 'require'
    ];
}