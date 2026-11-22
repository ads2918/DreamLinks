<?php

namespace App\Libraries;

use App\Models\DreamModel;
use App\Models\PromptModel;
use App\Models\TagModel;

/** Everything the Readings page shows. Shared by the page and the app API. */
class Readings
{
	public static function forUser(int $user_id, bool $enabled): array
	{
		$readings = array_values(array_filter(array_map(
			[PromptModel::class, 'reading'],
			model(PromptModel::class)->get_user_prompts($user_id, 'dreams', 60)
		)));
		foreach ($readings as $k => $reading) {
			$readings[$k]['moon'] = MoonPhase::name($reading['datetime']);
			$readings[$k]['moon_phase'] = MoonPhase::phase($reading['datetime']);
		}

		// Which symbols keep coming back, from the dreams themselves rather than the AI.
		$dream_model = model(DreamModel::class);
		$dreams = $dream_model->where('created_by', $user_id)->findAll();
		$tags = model(TagModel::class)->tags_for_dreams(array_column($dreams, 'id'));
		$counts = [];
		foreach ($dreams as $dream) {
			foreach (DreamSymbols::forDream($dream['title'], $dream['full_description'], $tags[$dream['id']] ?? []) as $s) {
				$counts[$s] = ($counts[$s] ?? 0) + 1;
			}
		}
		arsort($counts);

		return [
			'readings'    => $readings,
			'top_symbols' => array_slice($counts, 0, 8, true),
			'dream_count' => count($dreams),
			'waiting'     => count($dream_model->get_unprompted_dreams_by_user_id($user_id)),
			'enabled'     => $enabled,
			'tonight'     => date('Y-m-d'),
		];
	}
}
