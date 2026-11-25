<?php

use App\Libraries\MoonPhase;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Checked against published moon phases (timeanddate.com / USNO).
 *
 * @internal
 */
final class MoonPhaseTest extends CIUnitTestCase
{
	/** @return array<string, array{string, string}> */
	public static function knownNights(): array
	{
		return [
			'full moon 2024-04-23'     => ['2024-04-23', 'Full moon'],
			'new moon 2024-04-08'      => ['2024-04-08', 'New moon'],
			'first quarter 2024-04-15' => ['2024-04-15', 'First quarter'],
			'last quarter 2024-05-01'  => ['2024-05-01', 'Last quarter'],
			'full moon 2026-11-24'     => ['2026-11-24', 'Full moon'],
		];
	}

	/**
	 * @dataProvider knownNights
	 */
	public function testNamesKnownNights(string $date, string $expected): void
	{
		$this->assertSame($expected, MoonPhase::name($date));
	}

	public function testPhaseStaysInRange(): void
	{
		foreach (['1999-12-31', '2000-01-06', '2026-02-29', '2040-07-04'] as $date) {
			$p = MoonPhase::phase($date);
			$this->assertGreaterThanOrEqual(0, $p);
			$this->assertLessThan(1, $p);
		}
	}

	public function testIndexWrapsBackToNewMoon(): void
	{
		$this->assertSame(0, MoonPhase::index('2024-05-08')); // new moon
	}

	public function testFullDatetimeUsesItsDate(): void
	{
		// readings pass a prompt_datetime, not a bare date
		$this->assertSame(MoonPhase::name('2024-04-23'), MoonPhase::name('2024-04-23 21:16:00'));
	}
}
