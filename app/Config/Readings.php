<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * AI dream readings. The cron runs every minute, so a new dream gets its reading a minute or so
 * after it is saved. Each value can be overridden in .env, e.g. readings.dailyCap = 5
 */
class Readings extends BaseConfig
{
	/** Most readings one dreamer can get in a day. Dreams past the cap wait for tomorrow. */
	public int $dailyCap = 10;

	/** After a failed Gemini call, leave that dreamer alone this many minutes before trying again. */
	public int $retryAfterMinutes = 10;
}
