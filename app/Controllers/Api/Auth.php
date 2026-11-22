<?php

namespace App\Controllers\Api;

use App\Filters\ApiAuth;
use App\Models\ApiTokenModel;
use App\Models\UserModel;

class Auth extends ApiController
{
	/** POST api/v1/auth/login {email, password, device} → {token, user} */
	public function login()
	{
		// 10 tries a minute per address, the same message whatever went wrong
		if (! service('throttler')->check('api-login-' . md5($this->request->getIPAddress()), 10, MINUTE)) {
			return $this->respond(['error' => 'Too many tries. Wait a minute and try again.'], 429);
		}
		$in = $this->input();
		$email = trim((string) ($in['email'] ?? ''));
		$password = (string) ($in['password'] ?? '');
		$user = $email !== '' ? model(UserModel::class)->where('email', $email)->first() : null;
		if (! $user || $user['deleted'] || ! password_verify($password, $user['password'])) {
			return $this->respond(['error' => 'That email and password don\'t match.'], 401);
		}
		if ($user['status'] !== 'approved') {
			return $this->respond(['error' => 'Your account is waiting for an admin to approve it.'], 403);
		}
		$token = model(ApiTokenModel::class)->issue((int) $user['id'], (string) ($in['device'] ?? 'Android app'));
		ApiAuth::$user = $user;
		return $this->respond(['token' => $token, 'user' => Me::profile($user)]);
	}

	/** POST api/v1/auth/logout: forgets this device's token */
	public function logout()
	{
		model(ApiTokenModel::class)->delete($this->user()['token_id']);
		return $this->respond(['ok' => true]);
	}
}
