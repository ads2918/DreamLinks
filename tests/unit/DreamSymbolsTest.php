<?php

use App\Libraries\DreamSymbols;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class DreamSymbolsTest extends CIUnitTestCase
{
	public function testDetectsSymbolsFromRealDreamText(): void
	{
		$text = 'On beach by ocean all sudden huge orca whale came on to shore. Seemed to try to get me with teeth.';
		$found = DreamSymbols::detect($text);

		$this->assertContains('water', $found);
		$this->assertContains('sea creature', $found);
		$this->assertContains('teeth', $found);
	}

	public function testMatchesAtWordStartsOnly(): void
	{
		// "scare" must not count as "car", "cathedral" not as "cat", "examine" not as "ex"
		$this->assertSame([], DreamSymbols::detect('A scare in the cathedral made me examine it'));
	}

	public function testIsCaseInsensitive(): void
	{
		$this->assertContains('flying', DreamSymbols::detect('I FLEW over the city'));
	}

	public function testForDreamMergesTagsLowercasedAndUnique(): void
	{
		$symbols = DreamSymbols::forDream('Flying', 'I was flying', ['Flying', ' Cora ', '']);

		$this->assertSame(['flying', 'cora'], $symbols);
	}

	public function testNormalizeDecodesTagsSavedEscapedByTheOldCode(): void
	{
		$this->assertSame("mom's house", DreamSymbols::normalize('Mom&#039;s House'));
	}

	public function testEveryPatternCompiles(): void
	{
		foreach (DreamSymbols::LEXICON as $symbol => $pattern) {
			$this->assertNotFalse(@preg_match('/\b(?:' . $pattern . ')/i', ''), "Pattern for {$symbol} is invalid");
		}
	}

	public function testJsonIsSafeInsideAScriptTag(): void
	{
		$json = DreamSymbols::toJson();

		$this->assertStringNotContainsString('<', $json);
		$this->assertSame(DreamSymbols::LEXICON, json_decode($json, true));
	}
}
