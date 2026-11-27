<?php

namespace App\Libraries;

/**
 * One-time links sent by email (confirming a new account, deleting an account).
 * The token goes in the link; only its hash is kept, in the cache, until it expires or is used.
 */
class EmailLink
{
	/** @return string the token to put in the link */
	public static function issue(string $purpose, int $user_id, int $minutes): string
	{
		$token = bin2hex(random_bytes(32));
		cache()->save(self::key($purpose, $token), $user_id, $minutes * MINUTE);
		return $token;
	}

	/** @return int|null the user the link is for, while it is still valid */
	public static function user(string $purpose, string $token): ?int
	{
		if (! preg_match('/^[a-f0-9]{64}$/', $token)) {
			return null;
		}
		$user_id = cache(self::key($purpose, $token));
		return $user_id ? (int) $user_id : null;
	}

	public static function forget(string $purpose, string $token): void
	{
		cache()->delete(self::key($purpose, $token));
	}

	private static function key(string $purpose, string $token): string
	{
		return $purpose . '-' . hash('sha256', $token);
	}
}
