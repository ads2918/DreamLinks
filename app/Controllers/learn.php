<?php

namespace App\Controllers;
 
class Learn extends BaseController{
	public function index(): string{
		$data['title'] = 'Lucid Dreaming Induction: MILD, WILD, & Reality Check Guide';
		return view('templates/header', $data).view('pages/learn').view('templates/footer'); 
	}
}
