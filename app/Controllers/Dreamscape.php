<?php

namespace App\Controllers;

use App\Libraries\DreamSymbols;
use App\Libraries\MoonPhase;
use App\Models\DreamModel;
use App\Models\LinkModel;
use App\Models\PromptModel;
use App\Models\TagModel;

/**
 * A dreamer's symbol map: which symbols come up, and which ones show up in the same dreams.
 * ?with=ID lays another dreamer's map over yours, built only from dreams you are allowed to see.
 */
class Dreamscape extends BaseController
{
	private const MAX_NODES = 45;

	public function index($user_id)
	{
		$viewer_id = (int) $this->session->user_id;
		// Someone else's dreamscape opens as a comparison with yours.
		if ((int) $user_id !== $viewer_id) {
			return redirect()->to('user/' . $viewer_id . '/dreamscape?with=' . (int) $user_id);
		}

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

		$with_id = (int) $this->request->getGet('with');
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

		$data['title']      = 'Dreamscape';
		$data['page_js']    = 'dreamscape.js';
		$data['mine']       = $mine;
		$data['my_counts']  = $my_counts;
		$data['moon']       = $this->moon_counts($mine);
		$data['kin']        = array_slice(array_filter($kin, fn ($k) => $k['similarity'] > 0), 0, 5, true);
		$data['dreamers']   = $kin;
		$data['compare']    = $compare;
		$data['graph']      = $this->graph($mine, $theirs);
		$data['reading']    = $this->latest_reading($viewer_id);

		return view('templates/header', $data)
			. view('users/dreamscape', $data)
			. view('templates/footer', $data);
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
					$nodes[$s] ??= ['id' => $s, 'mine' => 0, 'theirs' => 0, 'spotted' => isset(DreamSymbols::LEXICON[$s])];
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
						$links[$key] ??= ['source' => $kept[$i], 'target' => $kept[$j], 'weight' => 0];
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
