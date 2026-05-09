<?php

namespace App\Controllers;
use App\Models\BlockModel; 
use App\Models\LinkModel; 
class Block extends BaseController
{
	
	public function unblock($id){	
		$model = model(BlockModel::class);
		$block_record = $model->find($id);
		if($block_record['blocked_by_user_id'] && $this->session->user_id){
			$unblocked = $model->unblock($id,$this->session->user_id);
			if($unblocked){
				return redirect()->back();
			}
		}
	}	
 
	public function blockLink($id){	 
		$model = model(BlockModel::class);
		if(!$model->block_exsists($id,$this->session->user_id) && $id != $this->session->user_id){
			$model->save([
				'blocked_user_id' => $id,
				'blocked_by_user_id' => $this->session->user_id,
				'blocked_datetime' => date('Y-m-d h:i:s',strtotime('now')),
			]);	
		}	
 
		$model2 = model(LinkModel::class);
		$model2->decline_links($id,$this->session->user_id);
		$remaining_pending = count($model2->user_pending_links($this->session->user_id));
		echo $remaining_pending;
		return '';
	}
 
}
 
