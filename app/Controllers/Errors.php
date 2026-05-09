<?php namespace App\Controllers;

namespace App\Controllers;
use App\Controllers\BaseController;

class Errors extends BaseController {
    public function show404() {
		$data['title'] = '404 - Not Found';
        $this->response->setStatusCode(404);
        // CI4 automatically looks inside 'app/Views'
        return view('templates/header', $data) .
			   view('errors/custom_404') .
			   view('templates/footer', $data);
    }
}