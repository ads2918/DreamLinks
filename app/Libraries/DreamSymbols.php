<?php

namespace App\Libraries;

/**
 * The fixed list of dream symbols DreamLinks looks for in dream text.
 *
 * Each symbol maps to a regex fragment that is matched at a word start, case-insensitive.
 * The same list is sent to the browser (see toJson) so the dream form highlights exactly
 * what the server will count, so keep the fragments to syntax that PHP and JavaScript share.
 */
class DreamSymbols
{
	public const LEXICON = [
		'water'      => 'water|ocean|seas?\b|lake|river|waves?\b|swim|flood|rain|beach|pool',
		'flying'     => 'fly|flew|flight|float|soar|levitat',
		'falling'    => 'fall|fell\b|drop(ped|ping)?\b',
		'chase'      => 'chas(e|ed|ing)|pursu|hunted|ran away|running from',
		'teeth'      => 'teeth|tooth',
		'house'      => 'house|home\b|rooms?\b|closet|basement|attic|hallway',
		'door'       => 'doors?\b|gates?\b|portal',
		'death'      => 'die\b|died|dead|death|dying|funeral|kill',
		'family'     => 'mom\b|mother|dad\b|father|parents?\b|sister|brother|grand(ma|pa|mother|father)|family|cousin',
		'childhood'  => 'childhood|child\b|kids?\b|little boy|little girl|young(er)? me',
		'school'     => 'school|class(room)?\b|teacher|exam\b|homework',
		'car'        => 'cars?\b|driv(e|ing|er)|road|truck|crash',
		'travel'     => 'travel|trip\b|airport|plane|train\b|relocat|journey|map\b',
		'lost'       => 'lost\b|can.?t find|maze|searching|looking for',
		'naked'      => 'naked|nude',
		'fire'       => 'fire|burn|flame|smoke',
		'light'      => 'light\b|glow|bright|sun\b|sunlight',
		'darkness'   => 'dark|shadow|pitch black',
		'demon'      => 'demon|monster|ghost|evil|creature',
		'spirit'     => 'god\b|jesus|angel|heaven|pray|spirit|soul',
		'mountain'   => 'mountain|hill|cliff',
		'forest'     => 'forest|woods\b|trees?\b|jungle',
		'snow'       => 'snow|ice\b|icy|frozen|winter',
		'baby'       => 'bab(y|ies)|pregnan',
		'wedding'    => 'wedding|marr(y|ied|iage)',
		'money'      => 'money|cash|gold\b|rich\b|wallet',
		'phone'      => 'phone|calling|text(ed|ing)?\b',
		'work'       => 'work\b|boss|job\b|office|coworker',
		'love'       => 'girlfriend|boyfriend|wife|husband|partner|kiss|crush\b|\bex\b',
		'friend'     => 'friends?\b|buddy',
		'crowd'      => 'crowd|party|strangers|tons of people',
		'trapped'    => 'trap|stuck|locked|cage|prison|barrier',
		'fight'      => 'fight|punch|gun\b|weapon|attack|war\b|knife',
		'music'      => 'music|songs?\b|sing|danc',
		'mirror'     => 'mirror|reflection',
		'lucid'      => 'lucid|realized i was dreaming|knew i was dreaming',
		'time'       => 'clock|late for|running late|time travel',
		'climbing'   => 'stairs|elevator|ladder|climb',
		'paralysis'  => 'paraly|can.?t move|couldn.?t move',
		'city'       => 'city|street|building|neon|downtown',
		'space'      => 'outer space|stars\b|planet|galaxy|moon\b|universe',
		'animal'     => 'dog\b|cat\b|horse|wolf|bear\b|lion|tiger',
		'sea creature' => 'whale|orca|shark|dolphin|fish\b|octopus',
		'snake'      => 'snakes?\b|serpent',
		'spider'     => 'spider|insect|bugs?\b',
		'bird'       => 'birds?\b|crow\b|raven|owl\b|eagle',
		'freedom'    => 'free\b|freedom|escape|escaped',
	];

	/** @return list<string> symbols found in the text, in lexicon order */
	public static function detect(string $text): array
	{
		$found = [];
		foreach (self::LEXICON as $symbol => $pattern) {
			if (preg_match('/\b(?:' . $pattern . ')/i', $text)) {
				$found[] = $symbol;
			}
		}
		return $found;
	}

	/**
	 * Every symbol a dream carries: its tags plus what the text mentions.
	 *
	 * @param list<string> $tags
	 * @return list<string> lower-case, unique
	 */
	public static function forDream(string $title, string $text, array $tags = []): array
	{
		$all = self::detect($title . ' ' . $text);
		foreach ($tags as $tag) {
			$tag = self::normalize($tag);
			if ($tag !== '') {
				$all[] = $tag;
			}
		}
		return array_values(array_unique($all));
	}

	public static function normalize(string $tag): string
	{
		return mb_strtolower(trim(html_entity_decode($tag, ENT_QUOTES)));
	}

	public static function toJson(): string
	{
		return json_encode(self::LEXICON, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
	}
}
