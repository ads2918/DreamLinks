<?php

namespace App\Controllers;
use App\Models\LinkModel; 
class Link extends BaseController
{
 
	public function index(): string{
		// $data['title'] = ucfirst('Home'); 
		// return view('templates/header', $data)
			// . view('pages/home')
			// . view('templates/footer');
	}
	
	public function requestLink($id){
		$session = \Config\Services::session();		
		$model = model(LinkModel::class);
		$links = $model->link_request_pending($session->user_id,$id);
		//only add link if link not already present and user cannot add link to self
		if(!count($links) && $id != $session->user_id){
			$model->save([
				'recipient_user_id' => $id,
				'request_user_id' => $session->user_id,
			]);	
		}
		
		echo 1;
	}
	
	public function acceptLink($id){
		$model = model(LinkModel::class);
		$link = $model->find($id);
		/* only recipient of link can accept link */
		if($this->session->user_id == $link['recipient_user_id']){
			$session = \Config\Services::session();
			$model = model(LinkModel::class);
			$model->save([
				'id' => $id,
				'request_approved' => 1,
				'request_approved_datetime' => date('Y-m-d h:i:s',strtotime('now')),
			]);	
			
			$remaining_pending = count($model->user_pending_links($this->session->user_id));
			echo $remaining_pending;
			 
		}
		return '';
	}
	
	public function unlink($id){	
		$model = model(LinkModel::class);
		$model_link = $model->find($id);
		if(($this->session->user_id == $model_link['request_user_id']) || ($this->session->user_id == $model_link['recipient_user_id'])){
			if($model_link['request_approved']){
				$unlink = $model->decline_link_by_id($id);
				if($unlink){
					return redirect()->back();
				}
			}
		}
	}
	
	public function blockLink($id){	 
		// $session = \Config\Services::session();
		// $model = model(LinkModel::class);
		// $model->save([
			// 'id' => $id,
			// 'request_approved' => 0,
			// 'block_flag' => 1,
			// 'block_datetime' => date('Y-m-d h:i:s',strtotime('now')),
        // ]);	
		
		// $remaining_pending = count($model->user_pending_links($session->user_id));
		// echo $remaining_pending;
		// return '';
	}
	
	public function declineLink($id){
		$model = model(LinkModel::class);
		$link = $model->find($id);
		/* only recipient of link can decline link */
		if($this->session->user_id == $link['recipient_user_id']){
			$session = \Config\Services::session();
			$model = model(LinkModel::class);
			$model->save([
				'id' => $id,
				'request_approved' => 0,
				'decline_flag' => 1,
				'decline_datetime' => date('Y-m-d h:i:s',strtotime('now')),
			]);	
			
			$remaining_pending = count($model->user_pending_links($session->user_id));
			echo $remaining_pending;
			return '';
		}
	}
}
 
