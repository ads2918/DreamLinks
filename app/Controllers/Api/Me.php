<?php

namespace App\Controllers\Api;

use App\Libraries\AccountDeletion;
use App\Models\UserModel;

class Me extends ApiController
{
	/** The signed-in user's own profile and settings. */
	public static function profile(array $u): array
	{
		return [
			'id'                       => (int) $u['id'],
			'username'                 => $u['username'],
			'email'                    => $u['email'],
			'bio'                      => $u['bio'],
			'avatar'                   => self::avatar($u['image']),
			'role'                     => $u['role'],
			'ai_analysis_enabled'      => (bool) $u['ai_analysis_enabled'],
			'default_dream_visibility' => $u['default_dream_visibility'] ?: 'private',
			'reminder_enabled'         => (bool) ($u['reminder_enabled'] ?? false),
			'reminder_hour'            => (int) ($u['reminder_hour'] ?? 8),
			'timezone'                 => $u['timezone'] ?? 'America/Indianapolis',
		];
	}

	/** GET api/v1/me */
	public function show()
	{
		return $this->respond(self::profile($this->user()));
	}

	/** POST api/v1/me: change any of bio, ai_analysis_enabled, default_dream_visibility, reminder_enabled, reminder_hour, timezone */
	public function update()
	{
		$in = $this->input();
		$rules = [
			'bio'                      => 'permit_empty|max_length[200]',
			'default_dream_visibility' => 'permit_empty|in_list[private,friends,public]',
			'reminder_hour'            => 'permit_empty|integer|greater_than_equal_to[4]|less_than_equal_to[12]',
			'timezone'                 => 'permit_empty|max_length[64]',
		];
		$validation = service('validation')->setRules($rules);
		if (! $validation->run($in)) {
			return $this->invalid($validation->getErrors());
		}
		if (isset($in['timezone']) && ! in_array($in['timezone'], \DateTimeZone::listIdentifiers(), true)) {
			return $this->invalid(['timezone' => 'Unknown time zone.']);
		}

		$fields = [];
		foreach (['bio', 'default_dream_visibility', 'timezone'] as $k) {
			if (isset($in[$k]) && $in[$k] !== '') {
				$fields[$k] = (string) $in[$k];
			}
		}
		foreach (['ai_analysis_enabled', 'reminder_enabled'] as $k) {
			if (array_key_exists($k, $in)) {
				$fields[$k] = filter_var($in[$k], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
			}
		}
		if (isset($in['reminder_hour']) && $in['reminder_hour'] !== '') {
			$fields['reminder_hour'] = (int) $in['reminder_hour'];
		}

		$model = model(UserModel::class);
		if ($fields) {
			$model->update($this->uid(), $fields);
		}
		return $this->respond(self::profile($model->find($this->uid())));
	}

	/** POST api/v1/me/delete {password}: deletes the account and everything in it, for good */
	public function delete()
	{
		if (! password_verify((string) ($this->input()['password'] ?? ''), $this->user()['password'])) {
			return $this->invalid(['password' => 'That password is not right.']);
		}
		if (! AccountDeletion::erase($this->uid())) {
			return $this->respond(['error' => 'Something went wrong and your account was not deleted. Try again.'], 500);
		}
		return $this->respond(['ok' => true]);
	}
}
