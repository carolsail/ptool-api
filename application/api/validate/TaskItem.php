<?php
namespace app\api\validate;

class TaskItem extends BaseValidate
{
    protected $rule =   [
        'title'  => 'require',
        'deadline' => 'myDeadlineRule'
    ];

    protected $message = [
    	'title' => '标题不能为空'
    ];

    protected $scene = [
    	'add' => [],
    	'edit' => []
    ];

    // deadline时间必须大于今日
    public function myDeadlineRule($value, $rule='', $data='', $field=''){
        if($value){
            return strtotime($value) > strtotime(date('Y-m-d'));
        }else{
            return true;
        }
    }
}