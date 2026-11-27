<?php

namespace App\Controllers;

use App\Libraries\AccountDeletion;
use App\Models\UserModel;

/**
 * account/delete: the public page for deleting an account (the Play Store links here too).
 * Signed in, the password confirms it. Signed out, or never confirmed, an emailed
 * link proves the address is yours.
 */
class Account extends BaseController
{
	/** GET account/delete */
	public function delete()
	{
		return $this->page($this->logged_in() ? 'password' : 'email');
	}

	/** POST account/delete */
	public function deleteSubmit()
	{
		helper('form');
		if ($this->logged_in()) {
			$user = model(UserModel::class)->find((int) $this->session->user_id);
			if (! $user || ! password_verify((string) $this->request->getPost('password'), $user['password'])) {
				session()->setFlashdata('error', 'That password is not right.');
				return redirect()->to('account/delete');
			}
			return $this->erase((int) $user['id']);
		}

		if (! $this->validate(['email' => 'required|valid_email|max_length[100]'])) {
			session()->setFlashdata('error', 'Enter the email you signed up with.');
			return redirect()->to('account/delete')->withInput();
		}
		// a few emails a minute per address, so the form can't be used to flood an inbox
		if (service('throttler')->check('account-delete-' . md5($this->request->getIPAddress()), 3, MINUTE)) {
			$email = trim((string) $this->request->getPost('email'));
			$user = model(UserModel::class)->where('email', $email)->first();
			if ($user) {
				$link = site_url('account/delete/confirm/' . AccountDeletion::issueLink((int) $user['id']));
				$this->send_email($user['email'], 'Delete your DreamLinks account',
					'Someone asked to delete the DreamLinks account for this email address. If it was you, open this link to finish: '
					. '<a href="' . esc($link, 'attr') . '">' . esc($link) . '</a><br><br>'
					. 'The link works for ' . AccountDeletion::LINK_MINUTES . ' minutes. If you did not ask for this, ignore this email and nothing will change.');
			}
		}
		// the same answer either way, so the form doesn't reveal who has an account
		session()->setFlashdata('success', 'If that email has an account, we have sent it a link to finish deleting it.');
		return redirect()->to('account/delete');
	}

	/** GET account/delete/confirm/{token}: opening the link only asks; the button below does the deleting */
	public function confirm($token = '')
	{
		$user_id = AccountDeletion::linkUser((string) $token);
		$user = $user_id ? model(UserModel::class)->find($user_id) : null;
		if (! $user) {
			session()->setFlashdata('error', 'That link has expired or was already used. Ask for a new one below.');
			return redirect()->to('account/delete');
		}
		return $this->page('confirm', ['token' => $token, 'account' => $user]);
	}

	/** POST account/delete/confirm */
	public function confirmSubmit()
	{
		$token = (string) $this->request->getPost('token');
		$user_id = AccountDeletion::linkUser($token);
		if (! $user_id) {
			session()->setFlashdata('error', 'That link has expired or was already used. Ask for a new one below.');
			return redirect()->to('account/delete');
		}
		AccountDeletion::forgetLink($token);
		return $this->erase($user_id);
	}

	/** GET account/deleted */
	public function deleted()
	{
		return $this->page('done');
	}

	private function erase(int $user_id)
	{
		if (! AccountDeletion::erase($user_id)) {
			session()->setFlashdata('error', 'Something went wrong and your account was not deleted. Try again, or use the contact page.');
			return redirect()->to('account/delete');
		}
		if ((int) $this->session->user_id === $user_id) {
			$this->session->destroy();
		}
		return redirect()->to('account/deleted');
	}

	private function page(string $step, array $data = [])
	{
		helper('form');
		return view('templates/header', ['title' => 'Delete account'])
			. view('users/delete-account', $data + ['step' => $step])
			. view('templates/footer');
	}
}
