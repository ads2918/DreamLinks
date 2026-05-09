<?php

namespace App\Controllers;
use App\Models\DreamModel;
use App\Models\LinkModel;
class Home extends BaseController
{
	
	public function landingPage(): string{
		$data['title'] = 'Lucid Dreaming Induction: MILD, WILD, & Reality Check Guide';
		return view('pages/landingPage'); 
	}
	
	public function index(): string{
		
		helper('url');
		$session = \Config\Services::session();
		$user_id = $session->get('user_id');
	
		
		$data['title'] = ucfirst('Welcome'); 

		if(isset($user_id)){
			$dream_model = model(DreamModel::class);
			$link = model(LinkModel::Class);
			$links = $link->user_approved_links($user_id);
			$linked_user_ids = array();
			if(!empty($links)){
				foreach($links as $link){
					if($link['request_user_id'] != $user_id){
						 $linked_user_ids[] = $link['request_user_id'];
					}elseif($link['recipient_user_id'] != $user_id){
						  $linked_user_ids[] = $link['recipient_user_id'];
					}
				}
			}
			 
			$public_dreams = $dream_model->get_front_page_dreams(25,1,$linked_user_ids,$session->get('user_id')); 
			$data['public_dreams'] = $public_dreams;
			return view('templates/header', $data).view('pages/home',$data).view('templates/footer');
		}else{
			return view('pages/landingPage'); 
		}
	}
}
