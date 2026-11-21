<?php

namespace App\Libraries;

/**
 * Moon phase for any date, from the mean synodic month.
 * Same reference new moon and math as DreamLinks.moonPhase in dreamlinks.js.
 */
class MoonPhase
{
	public const NAMES = [
		'New moon', 'Waxing crescent', 'First quarter', 'Waxing gibbous',
		'Full moon', 'Waning gibbous', 'Last quarter', 'Waning crescent',
	];

	private const SYNODIC = 29.530588853;
	private const NEW_MOON_2000 = 947182440; // 2000-01-06 18:14 UTC

	/** @return float 0..1, where 0 is new moon and 0.5 is full */
	public static function phase(string $date): float
	{
		// Noon local time stands in for "the night of" a dream date.
		$t = strtotime($date . ' 12:00:00');
		$days = ($t - self::NEW_MOON_2000) / 86400;
		$p = fmod($days / self::SYNODIC, 1);
		return $p < 0 ? $p + 1 : $p;
	}

	/** @return int 0..7, an index into NAMES */
	public static function index(string $date): int
	{
		return ((int) floor(self::phase($date) * 8 + 0.5)) % 8;
	}

	public static function name(string $date): string
	{
		return self::NAMES[self::index($date)];
	}
}
