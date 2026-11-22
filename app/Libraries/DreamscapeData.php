<?php

namespace App\Libraries;

use App\Models\DreamModel;
use App\Models\LinkModel;
use App\Models\PromptModel;
use App\Models\TagModel;

/**
 * A dreamer's symbol map: which symbols come up, and which ones show up in the same dreams.
 * Comparing with another dreamer only uses dreams the viewer is allowed to see.
 * Shared by the Dreamscape page and the app API.
 */
class DreamscapeData
{
	private const MAX_NODES = 45;

	public function build(int $viewer_id, int $with_id = 0): array
	{
		$linked = model(LinkModel::class)->linked_user_ids($viewer_id);
		$dreams = model(DreamModel::class);

		$mine = $this->with_symbols($dreams->visible_to($viewer_id, $linked, [$viewer_id])->findAll());

		// Everyone else whose dreams the viewer can see, for "dreamers like you" and the compare menu.
		$others = $this->with_symbols(
			$dreams->visible_to($viewer_id, $linked, null, false)->where('dreams.created_by !=', $viewer_id)->findAll(2000)
		);
		$by_dreamer = [];
		foreach ($others as $dream) {
			$by_dreamer[(int) $dream['created_by']]['name'] = $dream['username'];
			$by_dreamer[(int) $dream['created_by']]['image'] = $dream['image'];
			$by_dreamer[(int) $dream['created_by']]['dreams'][] = $dream;
		}

		$my_counts = $this->symbol_counts($mine);
		$kin = [];
		foreach ($by_dreamer as $id => $d) {
			$counts = $this->symbol_counts($d['dreams']);
			$kin[$id] = [
				'id'         => $id,
				'name'       => $d['name'],
				'image'      => $d['image'],
				'dreams'     => count($d['dreams']),
				'similarity' => $this->similarity($my_counts, $counts),
				'shared'     => array_keys(array_intersect_key($my_counts, $counts)),
				'linked'     => in_array($id, $linked, true),
			];
		}
		uasort($kin, fn ($a, $b) => [$b['similarity'], $b['dreams']] <=> [$a['similarity'], $a['dreams']]);

		$data = ['compare_note' => null];
		$compare = null;
		$theirs = [];
		if ($with_id && isset($by_dreamer[$with_id])) {
			$theirs = $by_dreamer[$with_id]['dreams'];
			$their_counts = $this->symbol_counts($theirs);
			$compare = $kin[$with_id] + [
				'only_mine'   => array_slice(array_keys(array_diff_key($my_counts, $their_counts)), 0, 8),
				'only_theirs' => array_slice(array_keys(array_diff_key($their_counts, $my_counts)), 0, 8),
			];
		} elseif ($with_id) {
			$data['compare_note'] = "That dreamer hasn't shared any dreams you can see yet.";
		}

		return $data + [
			'mine'        => $mine,
			'my_counts'   => $my_counts,
			'moon'        => $this->moon_counts($mine),
			'moon_detail' => $this->moon_detail($mine, $my_counts),
			'kin'         => array_slice(array_filter($kin, fn ($k) => $k['similarity'] > 0), 0, 5, true),
			'dreamers'    => $kin,
			'compare'     => $compare,
			'graph'       => $this->graph($mine, $theirs),
			'reading'     => $this->latest_reading($viewer_id),
		];
	}

	/** Adds a 'symbols' list to each dream: its tags plus what its text mentions. */
	private function with_symbols(array $dreams): array
	{
		$tags = model(TagModel::class)->tags_for_dreams(array_column($dreams, 'id'));
		foreach ($dreams as $k => $dream) {
			$dreams[$k]['symbols'] = DreamSymbols::forDream($dream['title'], $dream['full_description'], $tags[$dream['id']] ?? []);
		}
		return $dreams;
	}

	/** @return array<string, int> how many dreams each symbol appears in, most first */
	private function symbol_counts(array $dreams): array
	{
		$counts = [];
		foreach ($dreams as $dream) {
			foreach ($dream['symbols'] as $s) {
				$counts[$s] = ($counts[$s] ?? 0) + 1;
			}
		}
		arsort($counts);
		return $counts;
	}

	/** Weighted Jaccard: shared dream counts over combined dream counts, 0..1. */
	private function similarity(array $a, array $b): float
	{
		$min = $max = 0;
		foreach (array_keys($a + $b) as $s) {
			$min += min($a[$s] ?? 0, $b[$s] ?? 0);
			$max += max($a[$s] ?? 0, $b[$s] ?? 0);
		}
		return $max ? round($min / $max, 3) : 0.0;
	}

	/**
	 * For each moon phase: its dreams, and the symbols that come up in them. A symbol "stands out"
	 * when it shows up in a clearly bigger share of that phase's dreams than of all your dreams.
	 */
	private function moon_detail(array $dreams, array $all_counts): array
	{
		$total = max(count($dreams), 1);
		$phases = array_fill(0, 8, []);
		foreach ($dreams as $dream) {
			if ($dream['date']) {
				$phases[MoonPhase::index($dream['date'])][] = $dream;
			}
		}
		$detail = [];
		foreach ($phases as $i => $list) {
			$symbols = [];
			foreach ($this->symbol_counts($list) as $s => $n) {
				$share = $n / count($list);
				$usual = ($all_counts[$s] ?? $n) / $total;
				$symbols[] = ['symbol' => (string) $s, 'n' => $n, 'stands_out' => $n >= 2 && $share >= $usual * 1.5];
			}
			usort($symbols, fn ($a, $b) => [$b['stands_out'], $b['n']] <=> [$a['stands_out'], $a['n']]);
			$detail[$i] = [
				'name'    => MoonPhase::NAMES[$i],
				'count'   => count($list),
				'share'   => (int) round(count($list) / $total * 100),
				'symbols' => array_slice($symbols, 0, 8),
				'dreams'  => array_slice($list, 0, 8),
			];
		}
		return $detail;
	}

	/** @return list<int> dream counts for each of the 8 moon phases */
	private function moon_counts(array $dreams): array
	{
		$counts = array_fill(0, 8, 0);
		foreach ($dreams as $dream) {
			if ($dream['date']) {
				$counts[MoonPhase::index($dream['date'])]++;
			}
		}
		return $counts;
	}

	/**
	 * Nodes are symbols, sized by how many dreams carry them. A link joins two symbols that
	 * appear in the same dream; its weight is how many dreams they share. Only the most common
	 * symbols are kept so the map stays readable.
	 */
	private function graph(array $mine, array $theirs): array
	{
		$nodes = [];
		$sides = ['mine' => $mine, 'theirs' => $theirs];
		foreach ($sides as $side => $dreams) {
			foreach ($dreams as $dream) {
				foreach ($dream['symbols'] as $s) {
					$nodes[$s] ??= ['id' => (string) $s, 'mine' => 0, 'theirs' => 0, 'spotted' => isset(DreamSymbols::LEXICON[$s])];
					$nodes[$s][$side]++;
				}
			}
		}
		uasort($nodes, fn ($a, $b) => ($b['mine'] + $b['theirs']) <=> ($a['mine'] + $a['theirs']));
		$nodes = array_slice($nodes, 0, self::MAX_NODES, true);

		$links = [];
		$list = [];
		foreach ($sides as $side => $dreams) {
			foreach ($dreams as $dream) {
				$kept = array_values(array_filter($dream['symbols'], fn ($s) => isset($nodes[$s])));
				sort($kept);
				for ($i = 0; $i < count($kept); $i++) {
					for ($j = $i + 1; $j < count($kept); $j++) {
						$key = $kept[$i] . "\x1F" . $kept[$j];
						$links[$key] ??= ['source' => (string) $kept[$i], 'target' => (string) $kept[$j], 'weight' => 0];
						$links[$key]['weight']++;
					}
				}
				$list[] = [
					'id'      => (int) $dream['id'],
					'side'    => $side,
					'title'   => $dream['title'],
					'date'    => date('M j, Y', strtotime($dream['date'])),
					'owner'   => (int) $dream['created_by'],
					'symbols' => $kept,
				];
			}
		}

		return ['nodes' => array_values($nodes), 'links' => array_values($links), 'dreams' => $list];
	}

	/** The most recent AI reading, or null. */
	private function latest_reading(int $user_id): ?array
	{
		$latest = model(PromptModel::class)->get_latest_prompt($user_id, 'dreams');
		return empty($latest) ? null : PromptModel::reading($latest[0]);
	}
}
