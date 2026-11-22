<?php

namespace App\Controllers;

use App\Libraries\DreamscapeData;

/**
 * A dreamer's symbol map. ?with=ID lays another dreamer's map over yours.
 * The numbers come from App\Libraries\DreamscapeData, which the app API uses too.
 */
class Dreamscape extends BaseController
{
	public function index($user_id)
	{
		$viewer_id = (int) $this->session->user_id;
		// Someone else's dreamscape opens as a comparison with yours.
		if ((int) $user_id !== $viewer_id) {
			return redirect()->to('user/' . $viewer_id . '/dreamscape?with=' . (int) $user_id);
		}

		$data = (new DreamscapeData())->build($viewer_id, (int) $this->request->getGet('with'));
		$data['title']   = 'Dreamscape';
		$data['page_js'] = 'dreamscape.js';

		return view('templates/header', $data)
			. view('users/dreamscape', $data)
			. view('templates/footer', $data);
	}
}
