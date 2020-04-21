<?php
namespace app\api\validate;

class TaskItem extends Base
{
    protected $rule =   [
        'title'  => 'require'
    ];
}