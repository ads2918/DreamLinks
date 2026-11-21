<?php

namespace App\Controllers;

use App\Models\DreamModel;
use App\Models\UserModel;

/**
 * Morning nudge. Run hourly from cron:  php public/index.php cron/reminders
 * Emails each dreamer who turned reminders on, once a day, at their chosen hour in their
 * own time zone, unless they have already logged a dream for last night.
 */
class Reminder extends BaseController
{
	public function send()
	{
		$users = model(UserModel::class)
			->where('reminder_enabled', 1)
			->where('status', 'approved')
			->where('deleted', 0)
			->findAll();

		$sent = 0;
		foreach ($users as $user) {
			try {
				$now = new \DateTime('now', new \DateTimeZone($user['timezone'] ?: 'America/Indianapolis'));
			} catch (\Exception $e) {
				$now = new \DateTime('now', new \DateTimeZone('America/Indianapolis'));
			}
			$today = $now->format('Y-m-d');
			if ((int) $now->format('G') !== (int) $user['reminder_hour'] || $user['reminder_last_sent'] === $today) {
				continue;
			}
			$yesterday = (clone $now)->modify('-1 day')->format('Y-m-d');
			$logged = model(DreamModel::class)
				->where('created_by', $user['id'])
				->whereIn('date', [$yesterday, $today])
				->countAllResults();
			if ($logged) {
				continue;
			}

			$message = view('emails/reminder', ['user' => $user]);
			if ($this->send_email($user['email'], 'Anything from last night?', $message)) {
				model(UserModel::class)->save(['id' => $user['id'], 'reminder_last_sent' => $today]);
				$sent++;
			}
		}
		echo "Reminders sent: {$sent}" . PHP_EOL;
	}
}
