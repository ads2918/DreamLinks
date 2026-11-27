<?php

namespace App\Libraries;

/**
 * Deletes an account and everything tied to it, for good. Used by the settings page, the app and
 * the emailed link on account/delete (which also works for accounts still waiting for approval).
 *
 * The tables have no foreign keys, so each one is cleared here in one transaction.
 */
class AccountDeletion
{
	/** How long an emailed deletion link works. */
	public const LINK_MINUTES = 60;

	/** @return bool false when there was no such user or the database refused */
	public static function erase(int $user_id): bool
	{
		$db = db_connect();
		$user = $db->table('users')->select('id, image')->where('id', $user_id)->get()->getRowArray();
		if (! $user) {
			return false;
		}
		$dream_ids = array_column($db->table('dreams')->select('id')->where('created_by', $user_id)->get()->getResultArray(), 'id');

		$db->transStart();
		if ($dream_ids) {
			$db->table('tags')->whereIn('dream_id', $dream_ids)->delete();
			$db->table('dream_resonance')->whereIn('dream_id', $dream_ids)->delete();
			$db->table('dreams')->whereIn('id', $dream_ids)->delete();
		}
		$db->table('dream_resonance')->where('user_id', $user_id)->delete();
		$db->table('prompts')->where('for_user_id', $user_id)->delete();
		$db->table('links')->groupStart()->where('request_user_id', $user_id)->orWhere('recipient_user_id', $user_id)->groupEnd()->delete();
		$db->table('blocks')->groupStart()->where('blocked_user_id', $user_id)->orWhere('blocked_by_user_id', $user_id)->groupEnd()->delete();
		$db->table('api_tokens')->where('user_id', $user_id)->delete();
		$db->table('reports')->groupStart()->where('reporter_id', $user_id)->orWhere('user_id', $user_id)->groupEnd()->delete();
		$db->table('users')->where('id', $user_id)->delete();
		$db->transComplete();
		if (! $db->transStatus()) {
			return false;
		}

		$path = env('uploads.upload_path');
		$image = basename((string) $user['image']);
		if ($path && $image !== '' && is_file($path . $image)) {
			@unlink($path . $image);
		}
		return true;
	}

	/** A one-time token for the emailed link, good for LINK_MINUTES. */
	public static function issueLink(int $user_id): string
	{
		return EmailLink::issue('account-delete', $user_id, self::LINK_MINUTES);
	}

	/** @return int|null the user the link is for, while it is still valid */
	public static function linkUser(string $token): ?int
	{
		return EmailLink::user('account-delete', $token);
	}

	public static function forgetLink(string $token): void
	{
		EmailLink::forget('account-delete', $token);
	}
}
