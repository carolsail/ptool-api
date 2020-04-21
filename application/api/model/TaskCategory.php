<?php
namespace app\api\model;

class TaskCategory extends Base
{
	public function items(){
		return $this->hasMany('TaskItem', 'category_id');
	}
}