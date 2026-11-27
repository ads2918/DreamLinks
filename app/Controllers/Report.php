<?php

namespace App\Controllers;

use App\Models\DreamModel;
use App\Models\LinkModel;
use App\Models\ReportModel;
use App\Models\UserModel;

/** report?dream=ID or report?user=ID: tell the admin about a dream or dreamer that breaks the rules. */
class Report extends BaseController
{
	/** GET report */
	public function index()
	{
		helper('form');
		$viewer_id = (int) $this->session->user_id;
		$dream_id = (int) $this->request->getGet('dream');
		$user_id = (int) $this->request->getGet('user');
		$dream = null;
		if ($dream_id) {
			$dream = model(DreamModel::class)->visible_to($viewer_id, model(LinkModel::class)->linked_user_ids($viewer_id), null, false)
				->where('dreams.id', $dream_id)->first();
			$user_id = $dream ? (int) $dream['created_by'] : 0;
		}
		$user = $user_id && $user_id !== $viewer_id ? model(UserModel::class)->where('id', $user_id)->where('deleted', 0)->first() : null;
		if (! $user || ($dream_id && ! $dream)) {
			return redirect()->to('/');
		}
		$data = ['title' => 'Report', 'dream' => $dream, 'user' => $user, 'reasons' => ReportModel::REASONS];
		return view('templates/header', $data)
			. view('users/report', $data)
			. view('templates/footer');
	}

	/** POST report */
	public function submit()
	{
		$dream_id = (int) $this->request->getPost('dream_id') ?: null;
		$user_id = (int) $this->request->getPost('user_id') ?: null;
		$error = ReportModel::file((int) $this->session->user_id, $user_id, $dream_id,
			(string) $this->request->getPost('reason'), (string) $this->request->getPost('note'));
		if ($error) {
			session()->setFlashdata('error', $error);
			return redirect()->back()->withInput();
		}
		session()->setFlashdata('success', 'Thanks for telling us. If you don\'t want to see them again, you can block them from their profile.');
		return redirect()->to($user_id ? 'user/' . $user_id : '/');
	}
}
