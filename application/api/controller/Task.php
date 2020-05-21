<?php
namespace app\api\controller;
use app\api\validate\TaskItem;
use app\api\validate\TaskCategory;
use app\api\exception\GeneralException; 

class Task extends Base
{
	/**
	 * ======================== items =================================
	 */
	public function items(){
		$startRow = input('post.startRow', 0);
		$rowsPerPage = input('post.rowsPerPage', 10);
		$sortBy = input('post.sortBy', 'desc');
		$descending = input('post.descending');
		$filter = input('post.filter');

		// 根据最后一条timer，作为当前timer, 排除done的数据
		$last_timer = model('TaskTimer')->where('item_id', 'not in', function($query){
			$query->table('task_timer')->where('type', 'done')->field('item_id');
		})->order('id', 'desc')->limit(1)->find();

		$current_timers = [];
		if($last_timer){
			$seconds = intval($last_timer['start'] + $last_timer['duration'] - time());
			trace('====seconds===='.$seconds, 'alert');
			// 此时已经超时
			if($seconds <= 0 && $last_timer['type'] == 'start'){
				$item = model('TaskItem')->find($last_timer['item_id']);
				$item->save(['status' => 4, 'status_text' => 'overing', 'is_top' => 3, 'update_time' => time()]);
				$item->timers()->save([
					'type' => 'over',
					'start' => $last_timer['start'] + $last_timer['duration'],
					'duration' => 0,
					'create_time' => time(),
					'update_time' => time()
				]);
			}
			$current_timers = model('TaskTimer')->where('item_id', $last_timer['item_id'])->select();
		}


		$where = new \think\db\Where;
		if($filter['title']){
			$where['title'] = ['like', '%'.$filter['title'].'%'];
		}
		if($filter['status']){
			$where['status_text'] = ['in', $filter['status']];
		}

		$sort = $descending ? 'desc' : 'asc';

		$total = model('TaskItem')->where($where)->count();
		$lists = model('TaskItem')->with(['category', 'timers'])->where($where)->order(['is_top'=>'desc', 'is_urgent'=>'desc', $sortBy=>$sort])->limit($startRow, $rowsPerPage)->select();
		foreach($lists as $list){
			if($list['category']){
				$list['category']['label'] = $list['category']['title'];
				$list['category']['value'] = $list['category']['id'];
			}
		}


		return ajaxReturn(['total'=>$total, 'lists'=>$lists, 'current_timers'=>$current_timers]);
	}

	public function changeItemUrgent(){
		$is_urgent = input('post.is_urgent');
		$update['is_urgent'] = $is_urgent;
		if($is_urgent == 1){
			$update['update_time'] = time();
		}
		model('TaskItem')->where('id', input('post.id'))->update($update);
		return ajaxReturn('urgent change success');
	}

	public function addItem(){
		(new TaskItem())->goCheck();

		$task = input('post.');
		$item['title'] = $task['title'];
		$item['create_time'] = $item['update_time'] = time();
		if($task['deadline']) {
			$item['deadline'] = strtotime($task['deadline']);
		}
		if(isset($task['category']) && $task['category']) $item['category_id'] = $task['category']['id'];
		model('TaskItem')->save($item);
		return ajaxReturn('item add success');
	}

	public function deleteItem(){
		$item = model('TaskItem')->get(input('post.id'));
		$item->delete();
		return ajaxReturn('success');
	}

	public function updateItem(){
		(new TaskItem())->goCheck();

		$task = input('post.');
		$item = model('TaskItem')->get($task['id']);

		$data['title'] = $task['title'];
		$data['update_time'] = time();
		$data['deadline'] = $task['deadline'] ? strtotime($task['deadline']) : null;
		$data['category_id'] = (isset($task['category']) && $task['category']) ? $task['category']['id'] : null;
		$item->save($data);
		return ajaxReturn('item update success');
	}

	/**
	 * ======================== timer =================================
	 */
	public function addTimer(){
		$data = input('param.');
		
		$row = model('TaskItem')->find($data['item']['id']);
		$last_timer = $row->timers()->order('id','desc')->limit(1)->find();
		if($row){
			if($row['status']==5){
				throw new GeneralException(['msg'=>'task status has done']);
			}
		}else{
			throw new GeneralException(['msg'=>'no task row']);
		}

		$timer = [];
		$timer['create_time'] = time();
		$timer['update_time'] = time();

		switch($data['type']){
			case 'start':
				if(!($data['hours'] || $data['minutes'])){
					throw new GeneralException(['msg'=>'duration can not be empty']);
				}

				if($last_timer){
					throw new GeneralException(['msg'=>'timer status has changed']);
				}

				$timer['type'] = $data['type'];
				$timer['start'] = time();
				$timer['duration'] = $data['hours'] * 3600 + $data['minutes'] * 60;
				$timer['remark'] = $data['remark'];
				$row->save(['status' => 2, 'status_text' => 'pending', 'is_top' => 3, 'update_time'=> time()]);
				$row->timers()->save($timer);
			break;
			case 'pause':
				if(!($last_timer['type']=='start' || $last_timer['type']=='over')){
					throw new GeneralException(['msg'=>'timer status has changed']);
				}

				$timer['type'] = $data['type'];
				$timer['start'] = time();
				$timer['duration'] = $last_timer['start'] + $last_timer['duration'] - time();
				$timer['remark'] = $data['remark'];
				$row->save(['status' => 3, 'status_text' => 'paused', 'is_top' => 2, 'update_time'=> time()]);
				$row->timers()->save($timer);
			break;
			case 'resume':
				if($last_timer['type']!='pause'){
					throw new GeneralException(['msg'=>'timer status has changed']);
				}

				if($last_timer['duration'] > 0){
					$type = 'start';
					$update = [
						'status' => 2,
						'status_text' => 'pending',
						'is_top' => 3,
						'update_time'=> time()
					];
				}else{
					$type = 'over';
					$update = [
						'status' => 4,
						'status_text' => 'overing',
						'is_top' => 3,
						'update_time'=> time()
					];
				}
				$timer['type'] = $type;
				$timer['start'] = time();
				$timer['duration'] = $last_timer['duration'];
				$timer['remark'] = $data['remark'];
				$row->save($update);
				$row->timers()->save($timer);
			break;
			case 'done':
				if($last_timer['type']=='done'){
					throw new GeneralException(['msg'=>'timer status has changed']);
				}

				if($last_timer['type']=='start' || $last_timer['type']=='over'){
					$duration = $last_timer['start'] + $last_timer['duration'] - time();
				}

				if($last_timer['type']=='pause'){
					$duration = $last_timer['duration'];
				}
				$timer['type'] = $data['type'];
				$timer['start'] = time();
				$timer['duration'] = $duration;
				$timer['remark'] = $data['remark'];
				$row->save(['status'=> 5, 'status_text'=>'done', 'is_top'=>0, 'update_time'=>time()]);
				$row->timers()->save($timer);
			break;
		}

		return ajaxReturn('success');
	}

	/**
	 * ========================== categories ==================================
	 */
	public function searchCategory(){
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
		return ajaxReturn($lists);
	}

	public function categories(){
		$startRow = input('post.startRow', 0);
		$rowsPerPage = input('post.rowsPerPage', 10);
		$sortBy = input('post.sortBy', 'desc');
		$descending = input('post.descending');
		$filter = input('post.filter');

		$where = new \think\db\Where;
		if($filter['title']){
			$where['title'] = ['like', '%'.$filter['title'].'%'];
		}

		$sort = $descending ? 'desc' : 'asc';

		$total = model('TaskCategory')->where($where)->count();
		$lists = model('TaskCategory')->with(['items', 'items.timers'])->where($where)->order(['is_active'=>'desc', $sortBy=>$sort])->limit($startRow, $rowsPerPage)->select();
		foreach($lists as $list){
			$list['is_active'] = (bool)$list['is_active'];
		}
		return ajaxReturn(['total'=>$total, 'lists'=>$lists]);
	}

	public function changeCategoryActive(){
		$is_active = input('post.is_active');
		$update['is_active'] = $is_active;
		$update['update_time'] = time();
		model('TaskCategory')->where('id', input('post.id'))->update($update);
		return ajaxReturn('active change success');
	}

	public function addCategory(){
		(new TaskCategory())->goCheck();

		$category = input('post.');
		$item['title'] = $category['title'];
		$item['create_time'] = $item['update_time'] = time();
		model('TaskCategory')->save($item);
		return ajaxReturn('success');
	}

	public function deleteCategory(){
		$item = model('TaskCategory')->get(input('post.id'));
		$item->delete();
		return ajaxReturn('success');
	}

	public function updateCategory(){
		(new TaskCategory())->goCheck();

		$category = input('post.');
		$item = model('TaskCategory')->get($category['id']);
		$data['title'] = $category['title'];
		$data['remark'] = trim($category['remark']);
		$data['update_time'] = time();
		$item->save($data);
		return ajaxReturn('success');
	}

	/**
	 * ======================== deadline =================================
	 */
	public function deadlines(){
		$where = [
            'is_deadline' => 1,
            'deadline_check' => 0
        ];  
        $total = model('TaskItem')->where($where)->count();
        $lists = model('TaskItem')->where($where)->limit(5)->select();
        return ajaxReturn(['total'=>$total, 'lists'=>$lists]);
	}

	/**
	 * 当天之前的所有deadline
	 * 条件: deadline > 完成任务时间
	 * is_deadline置为1
	 */
	public function deadlineMark(){
		$now = strtotime(date('Y-m-d'));
        $where = [
            ['deadline_daily', '<', $now],
            ['deadline', '>', 0]
        ];
        $tasks = model('TaskItem')->where($where)->select();
        $deadlines = [];
        foreach($tasks as $task){
            //今天檢查昨天的deadline（以天爲單位）
            if($now>$task['deadline']){
                //task狀態為done
                if($task['status']==5){
                    //和timer最後一條完成時間比較
                    $timer = $task->timers()->where('type','done')->order('id','desc')->find();
                    if($timer){
                        $endDayTime = strtotime(date('Y-m-d', $timer['start']));
                        if($task['deadline']<$endDayTime){
                            //標識deadline
                            $deadlines[] = [
                                'id' => $task['id'],
                                'is_deadline' => 1,
                                'deadline_daily' => $now
                            ];
                        }
                    }else{
                        //標識deadline
                        $deadlines[] = [
                            'id' => $task['id'],
                            'is_deadline' => 1,
                            'deadline_daily' => $now
                        ];
                    }
                }else{
                    //標識deadline
                    $deadlines[] = [
                        'id' => $task['id'],
                        'is_deadline' => 1,
                        'deadline_daily' => $now
                    ];
                }
            }
        }
        if($deadlines){
            //修改task中deadline信息
            model('TaskItem')->saveAll($deadlines);
        }
        return ajaxReturn(['total'=>count($deadlines), 'lists'=>$deadlines]);
	}
	/**
	 * 将is_deadline=1 标志为已读
	 */
	public function changeDeadlineCheck(){
		$id = input('post.id');
        if($id){
            $task = model('TaskItem')->find($id);
            if($task->is_deadline && !$task->deadline_check){
                $task->save(['deadline_check'=>1]);
            }
        }else{
            $tasks = model('TaskItem')->where(['is_deadline'=>1, 'deadline_check'=>0])->select();
            $list = [];
            foreach($tasks as $task){
                $list[] = ['id'=>$task['id'], 'deadline_check'=>1];
            }
            model('TaskItem')->saveAll($list);
        }
        return ajaxReturn('success');
	}

}