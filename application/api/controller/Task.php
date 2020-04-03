<?php
namespace app\api\controller;

class Task extends Base
{
	public function items(){
		$startRow = input('post.startRow', 0);
		$rowsPerPage = input('post.rowsPerPage', 10);
		$sortBy = input('post.sortBy', 'desc');
		$descending = input('post.descending');
		$filter = input('post.filter');

		$where = new \think\db\Where;
		if($filter['title']){
			$where['title'] = ['like', '%'.$filter['title'].'%'];
		}
		if($filter['status']){
			$where['status_text'] = ['in', $filter['status']];
		}

		$sort = $descending ? 'desc' : 'asc';

		$total = model('TaskItem')->where($where)->count();
		$lists = model('TaskItem')->where($where)->order($sortBy, $sort)->limit($startRow, $rowsPerPage)->select();
		return ajaxReturn(['total'=>$total, 'lists'=>$lists], true);
	}

	public function changeUrgent(){
		model('TaskItem')->where('id', input('post.id'))->update(['is_urgent'=>input('post.is_urgent')]);
		return ajaxReturn('urgent change success', true);
	}

	public function getCategories(){
		$lists = model('TaskCategory')->order('create_time', 'desc')->limit(10)->select();
		foreach($lists as $list){
			$list['label'] = $list['title'];
			$list['value'] = $list['id'];
		}
		return ajaxReturn($lists, true);
	}
}