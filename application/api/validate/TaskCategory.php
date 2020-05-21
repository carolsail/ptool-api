<?php
namespace app\api\validate;

class TaskCategory extends BaseValidate
{
    protected $rule =   [
        'title'  => 'require'
    ];
}