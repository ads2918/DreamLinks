<?php

namespace App\Controllers;
use App\Models\DreamModel;
use App\Models\LinkModel;
use App\Libraries\DreamFeed;
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
			$user_id = (int) $user_id;
			$linked_user_ids = model(LinkModel::class)->linked_user_ids($user_id);
			$public_dreams = model(DreamModel::class)->get_front_page_dreams(25,0,$linked_user_ids,$user_id);
			$data['public_dreams'] = DreamFeed::decorate($public_dreams, $user_id);
			$data['pulse'] = DreamFeed::pulse($user_id, $linked_user_ids);
			return view('templates/header', $data).view('pages/home',$data).view('templates/footer');
		}else{
			$data['title'] = 'AI Dream Analysis & Social Dream Journaling';
			$data['description'] = 'Log your dreams, discover patterns with AI analysis, track the moon, and share dreams with the people you trust on DreamLinks.';
			return view('templates/header', $data).view('pages/landingPage').view('templates/footer');
		}
	}
}
