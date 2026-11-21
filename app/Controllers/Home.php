<?php

namespace App\Controllers;
use App\Models\DreamModel;
use App\Models\LinkModel;
use App\Models\TagModel;
use App\Models\ResonanceModel;
use App\Libraries\DreamSymbols;
use App\Libraries\MoonPhase;
class Home extends BaseController
{
	
	public function landingPage(): string{
		$data['title'] = 'Lucid Dreaming Induction: MILD, WILD, & Reality Check Guide';
		return view('pages/landingPage'); 
	}
	
	public function index(): string{
		
		helper('url');
		$session = \Config\Services::session();
		$user_id = $session->get('user_id');
	
		
		$data['title'] = ucfirst('Welcome'); 

		if(isset($user_id)){
			$user_id = (int) $user_id;
			$linked_user_ids = model(LinkModel::class)->linked_user_ids($user_id);
			$public_dreams = model(DreamModel::class)->get_front_page_dreams(25,0,$linked_user_ids,$user_id);
			$dream_ids = array_column($public_dreams, 'id');

			$tags = model(TagModel::class)->tags_for_dreams($dream_ids);
			$resonance = model(ResonanceModel::class);
			$counts = $resonance->counts_for($dream_ids);
			$mine = $resonance->marked_by($user_id, $dream_ids);
			foreach($public_dreams as $key => $dream){
				$public_dreams[$key]['symbols'] = DreamSymbols::forDream($dream['title'], $dream['full_description'], $tags[$dream['id']] ?? []);
				$public_dreams[$key]['moon'] = MoonPhase::name($dream['date']);
				$public_dreams[$key]['resonance'] = $counts[$dream['id']] ?? 0;
				$public_dreams[$key]['resonated'] = in_array((int) $dream['id'], $mine, true);
			}

			$data['public_dreams'] = $public_dreams;
			$data['pulse'] = $this->commons_pulse($user_id, $linked_user_ids);
			return view('templates/header', $data).view('pages/home',$data).view('templates/footer');
		}else{
			$data['title'] = 'AI Dream Analysis & Social Dream Journaling';
			$data['description'] = 'Log your dreams, discover patterns with AI analysis, track the moon, and share dreams with the people you trust on DreamLinks.';
			return view('templates/header', $data).view('pages/landingPage').view('templates/footer');
		}
	}

	/**
	 * The symbols showing up most across dreams the viewer can see from the last 30 days,
	 * counted by how many different dreamers had them. Empty when there is too little to say.
	 *
	 * @return list<array{symbol: string, dreamers: int}>
	 */
	private function commons_pulse(int $user_id, array $linked_user_ids): array{
		$recent = model(DreamModel::class)->visible_to($user_id, $linked_user_ids, null, false)
										  ->where('dreams.date >=', date('Y-m-d', strtotime('-30 days')))
										  ->findAll(300);
		$tags = model(TagModel::class)->tags_for_dreams(array_column($recent, 'id'));
		$dreamers = [];
		foreach($recent as $dream){
			foreach(DreamSymbols::forDream($dream['title'], $dream['full_description'], $tags[$dream['id']] ?? []) as $symbol){
				$dreamers[$symbol][$dream['created_by']] = true;
			}
		}
		$pulse = [];
		foreach($dreamers as $symbol => $who){
			if(count($who) >= 2){
				$pulse[] = ['symbol' => $symbol, 'dreamers' => count($who)];
			}
		}
		usort($pulse, fn ($a, $b) => $b['dreamers'] <=> $a['dreamers']);
		return array_slice($pulse, 0, 6);
	}
}
