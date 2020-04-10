<?php
namespace app\api\controller;
use app\api\validate\Task as TaskValidate;

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
		$lists = model('TaskItem')->with('category')->where($where)->order($sortBy, $sort)->limit($startRow, $rowsPerPage)->select();
		foreach($lists as $list){
			if($list['category']){
				$list['category']['label'] = $list['category']['title'];
				$list['category']['value'] = $list['category']['id'];
			}
		}
		return ajaxReturn(['total'=>$total, 'lists'=>$lists], true);
	}

	public function changeUrgent(){
		model('TaskItem')->where('id', input('post.id'))->update(['is_urgent'=>input('post.is_urgent')]);
		return ajaxReturn('urgent change success', true);
	}

	public function getCategories(){
		$where = new \think\db\Where;
		if(input('post.search')){
			$where['title'] = ['like', '%'.input('post.search').'%'];
		}
		$where['is_active'] = ['=', 1];
		$lists = model('TaskCategory')->where($where)->order('create_time', 'desc')->limit(10)->select();
		foreach($lists as $list){
			$list['label'] = $list['title'];
			$list['value'] = $list['id'];
		}
		return ajaxReturn($lists, true);
	}

	public function addItem(){
		$task = input('post.');
		$validate = new TaskValidate();
		if (!$validate->check($task)) {
           return ajaxReturn($validate->getError());
        }
		$item['title'] = $task['title'];
		$item['create_time'] = $item['update_time'] = time();
		if($task['deadline']) {
			$deadline = strtotime($task['deadline']);
			if($deadline <= time()){
				return ajaxReturn('Deadline incorrect');
			}
			$item['deadline'] = $deadline;
		}
		if(isset($task['category']) && $task['category']) $item['category_id'] = $task['category']['id'];
		model('TaskItem')->save($item);
		return ajaxReturn('success', true);
	}

	public function deleteItem(){
		$item = model('TaskItem')->get(input('post.id'));
		$item->delete();
		return ajaxReturn('success', true);
	}

	public function updateItem(){
		$task = input('post.');
		$item = model('TaskItem')->get($task['id']);
		if($item->status != 1){
			return ajaxReturn('status has change, can not be update');
		}

		$data['title'] = $task['title'];
		$data['update_time'] = time();

		$deadline = null;
		if($task['deadline']) {
			$deadline = strtotime($task['deadline']);
			if($deadline <= time()){
				return ajaxReturn('Deadline incorrect');
			}
		}
		$data['deadline'] = $deadline;

		$data['category_id'] = (isset($task['category']) && $task['category']) ? $task['category']['id'] : null;
		$item->save($data);
		return ajaxReturn('success', true);
	}
}