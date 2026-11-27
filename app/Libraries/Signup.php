<?php

namespace App\Libraries;

use App\Controllers\BaseController;
use App\Models\UserModel;

/**
 * Anyone can sign up; the account works once its email address is confirmed.
 * Accounts from before open sign-up ('pending_approval') confirm the same way.
 */
class Signup
{
	public const LINK_MINUTES = 48 * 60;

	/** Statuses that are waiting on the emailed link. */
	public const UNCONFIRMED = ['unconfirmed', 'pending_approval'];

	/** Emails the confirmation link. @return bool whether the email went out */
	public static function sendConfirmation(array $user): bool
	{
		$link = site_url('user/confirm/' . EmailLink::issue('confirm', (int) $user['id'], self::LINK_MINUTES));
		$email = service('email');
		$email->setFrom(BaseController::ADMIN_EMAIL, 'DreamLinks');
		$email->setTo($user['email']);
		$email->setSubject('Confirm your DreamLinks account');
		$email->setMessage('<h1>Welcome to DreamLinks</h1><p>Hi ' . esc($user['username']) . '. Open this link to confirm your email and start logging dreams:</p>'
			. '<p><a href="' . esc($link, 'attr') . '">' . esc($link) . '</a></p>'
			. '<p>The link works for two days. If you did not sign up, ignore this email and the account stays locked.</p>Sincerely,<br>DreamLinks Team');
		if (! $email->send(false)) {
			log_message('error', 'Confirmation email to user {id} failed: {debug}', ['id' => $user['id'], 'debug' => $email->printDebugger(['headers'])]);
			return false;
		}
		return true;
	}

	/** Confirms the account a link is for. @return array|null the user, or null when the link is bad or used */
	public static function confirm(string $token): ?array
	{
		$user_id = EmailLink::user('confirm', $token);
		$model = model(UserModel::class);
		$user = $user_id ? $model->where('id', $user_id)->where('deleted', 0)->first() : null;
		if (! $user) {
			return null;
		}
		EmailLink::forget('confirm', $token);
		if (in_array($user['status'], self::UNCONFIRMED, true)) {
			$model->update($user_id, ['status' => 'approved']);
		}
		return $user;
	}
}
