<?php
namespace app\api\model;

class TaskItem extends Base
{
	public function category(){
		return $this->belongsTo('TaskCategory', 'category_id');
	}
}