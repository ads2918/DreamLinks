<?php

namespace App\Models;

use App\Controllers\BaseController;
use CodeIgniter\Model;

/** Reports of a dream or a dreamer. File them with ReportModel::file(); the website and the app both do. */
class ReportModel extends Model
{
	protected $table         = 'reports';
	protected $allowedFields = ['reporter_id', 'user_id', 'dream_id', 'reason', 'note', 'created_at'];

	public const REASONS = [
		'harassment' => 'Harassment or hate',
		'sexual'     => 'Sexual content',
		'violence'   => 'Violence or threats',
		'self_harm'  => 'Self-harm',
		'spam'       => 'Spam',
		'other'      => 'Something else',
	];

	/**
	 * Saves a report and emails it to the admin. The dream must be one the reporter can see.
	 * @return string|null an error to show, or null when it was filed
	 */
	public static function file(int $reporter_id, ?int $user_id, ?int $dream_id, string $reason, string $note): ?string
	{
		if (! isset(self::REASONS[$reason])) {
			return 'Pick a reason.';
		}
		if ($dream_id) {
			$linked = model(LinkModel::class)->linked_user_ids($reporter_id);
			$dream = model(DreamModel::class)->visible_to($reporter_id, $linked, null, false)->where('dreams.id', $dream_id)->first();
			if (! $dream) {
				return 'That dream is not there any more.';
			}
			$user_id = (int) $dream['created_by'];
		}
		$user = $user_id ? model(UserModel::class)->where('id', $user_id)->where('deleted', 0)->first() : null;
		if (! $user || (int) $user['id'] === $reporter_id) {
			return 'That dreamer is not there any more.';
		}
		if (! service('throttler')->check('report-' . $reporter_id, 10, HOUR)) {
			return 'That is a lot of reports. Try again in an hour.';
		}

		$note = mb_substr(trim($note), 0, 500);
		model(self::class)->insert([
			'reporter_id' => $reporter_id,
			'user_id'     => (int) $user['id'],
			'dream_id'    => $dream_id ?: null,
			'reason'      => $reason,
			'note'        => $note,
			'created_at'  => date('Y-m-d H:i:s'),
		]);

		$reporter = model(UserModel::class)->find($reporter_id);
		$what = $dream_id
			? 'the dream "' . esc($dream['title']) . '" by ' . esc($user['username']) . ' (dream #' . $dream_id . ')'
			: 'the dreamer ' . esc($user['username']) . ' (user #' . (int) $user['id'] . ')';
		$email = service('email');
		$email->setFrom(BaseController::ADMIN_EMAIL, 'DreamLinks reports');
		$email->setTo(BaseController::ADMIN_EMAIL);
		$email->setSubject('Report: ' . self::REASONS[$reason]);
		$email->setMessage(esc($reporter['username'] ?? 'Someone') . ' reported ' . $what . ' for <b>' . self::REASONS[$reason] . '</b>.'
			. ($note !== '' ? '<br><br>Their note:<br>' . nl2br(esc($note)) : '')
			. ($dream_id ? '<br><br>The dream:<br>' . nl2br(esc($dream['full_description'])) : '')
			. '<br><br><a href="' . site_url('user/' . (int) $user['id']) . '">Open their profile</a>');
		$email->send();
		return null;
	}
}
