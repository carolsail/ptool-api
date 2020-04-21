<?php
namespace app\api\model;

class TaskItem extends Base
{
	public function category(){
		return $this->belongsTo('TaskCategory', 'category_id');
	}

	public function timers(){
		return $this->hasMany('TaskTimer', 'item_id');
	}
}