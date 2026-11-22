<?php

namespace App\Libraries;

use App\Models\DreamModel;
use App\Models\ResonanceModel;
use App\Models\TagModel;

/** Adds what a dream card shows to plain dream rows. Used by the feed, profiles and the app API. */
class DreamFeed
{
	/**
	 * Each dream gains: tags, symbols (tags plus what the text mentions), moon (phase name),
	 * resonance (how many dreamers marked it) and resonated (whether the viewer did).
	 */
	public static function decorate(array $dreams, int $viewer_id): array
	{
		$ids = array_column($dreams, 'id');
		$tags = model(TagModel::class)->tags_for_dreams($ids);
		$resonance = model(ResonanceModel::class);
		$counts = $resonance->counts_for($ids);
		$marked = $resonance->marked_by($viewer_id, $ids);
		foreach ($dreams as $k => $dream) {
			$dream_tags = $tags[$dream['id']] ?? [];
			$dreams[$k]['tags'] = $dream_tags;
			$dreams[$k]['symbols'] = DreamSymbols::forDream($dream['title'], $dream['full_description'], $dream_tags);
			$dreams[$k]['moon'] = MoonPhase::name($dream['date']);
			$dreams[$k]['resonance'] = $counts[$dream['id']] ?? 0;
			$dreams[$k]['resonated'] = in_array((int) $dream['id'], $marked, true);
		}
		return $dreams;
	}

	/**
	 * The symbols showing up most across dreams the viewer can see from the last 30 days,
	 * counted by how many different dreamers had them. Empty when there is too little to say.
	 *
	 * @return list<array{symbol: string, dreamers: int}>
	 */
	public static function pulse(int $user_id, array $linked_user_ids): array
	{
		$recent = model(DreamModel::class)->visible_to($user_id, $linked_user_ids, null, false)
										  ->where('dreams.date >=', date('Y-m-d', strtotime('-30 days')))
										  ->findAll(300);
		$tags = model(TagModel::class)->tags_for_dreams(array_column($recent, 'id'));
		$dreamers = [];
		foreach ($recent as $dream) {
			foreach (DreamSymbols::forDream($dream['title'], $dream['full_description'], $tags[$dream['id']] ?? []) as $symbol) {
				$dreamers[$symbol][$dream['created_by']] = true;
			}
		}
		$pulse = [];
		foreach ($dreamers as $symbol => $who) {
			if (count($who) >= 2) {
				$pulse[] = ['symbol' => (string) $symbol, 'dreamers' => count($who)];
			}
		}
		usort($pulse, fn ($a, $b) => $b['dreamers'] <=> $a['dreamers']);
		return array_slice($pulse, 0, 6);
	}
}
