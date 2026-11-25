<?php

namespace App\Controllers\Api;

use App\Libraries\DreamscapeData;
use App\Libraries\DreamSymbols;
use App\Libraries\MoonPhase;
use App\Libraries\Readings;

/** Dreamscape, readings and the symbol list, in the same shapes the website pages use. */
class Insights extends ApiController
{
	/** GET api/v1/dreamscape?with={user id} */
	public function dreamscape()
	{
		$d = (new DreamscapeData())->build($this->uid(), (int) $this->request->getGet('with'));
		$moon = [];
		foreach ($d['moon_detail'] as $i => $phase) {
			$moon[] = [
				'phase'   => $i,
				'name'    => $phase['name'],
				'count'   => $phase['count'],
				'share'   => $phase['share'],
				'symbols' => $phase['symbols'],
				'dreams'  => array_map(fn ($x) => ['id' => (int) $x['id'], 'title' => $x['title'], 'date' => $x['date']], $phase['dreams']),
			];
		}
		$kin = fn ($k) => [
			'id' => $k['id'], 'username' => $k['name'], 'avatar' => self::avatar($k['image']), 'dreams' => $k['dreams'],
			'similarity' => $k['similarity'], 'shared' => array_slice($k['shared'], 0, 8), 'linked' => $k['linked'],
		];
		$top = [];
		foreach (array_slice($d['my_counts'], 0, 10, true) as $symbol => $n) {
			$top[] = ['symbol' => (string) $symbol, 'n' => $n];
		}
		return $this->respond([
			'dream_count'  => count($d['mine']),
			'top_symbols'  => $top,
			'moon'         => $moon,
			'graph'        => $d['graph'],
			'kin'          => array_values(array_map($kin, $d['kin'])),
			'dreamers'     => array_values(array_map($kin, $d['dreamers'])),
			'compare'      => $d['compare'] ? $kin($d['compare']) + ['only_mine' => $d['compare']['only_mine'], 'only_theirs' => $d['compare']['only_theirs']] : null,
			'compare_note' => $d['compare_note'],
		]);
	}

	/** GET api/v1/readings */
	public function readings()
	{
		$r = Readings::forUser($this->uid(), (bool) $this->user()['ai_analysis_enabled']);
		$top = [];
		foreach ($r['top_symbols'] as $symbol => $n) {
			$top[] = ['symbol' => (string) $symbol, 'n' => $n];
		}
		return $this->respond([
			'enabled'     => $r['enabled'],
			'waiting'     => $r['waiting'],
			'capped'      => $r['capped'],
			'dream_count' => $r['dream_count'],
			'tonight'     => ['name' => MoonPhase::name($r['tonight']), 'phase' => MoonPhase::phase($r['tonight'])],
			'top_symbols' => $top,
			'readings'    => array_map(fn ($x) => [
				'date' => $x['datetime'], 'overview' => $x['overview'], 'patterns' => $x['patterns'], 'symbols' => $x['symbols'],
				'celestial' => $x['celestial'], 'moon' => $x['moon'], 'moon_phase' => $x['moon_phase'],
			], $r['readings']),
		]);
	}

	/** GET api/v1/symbols : the lexicon, so the app can spot symbols while the user types */
	public function symbols()
	{
		return $this->respond(['symbols' => DreamSymbols::LEXICON]);
	}
}
