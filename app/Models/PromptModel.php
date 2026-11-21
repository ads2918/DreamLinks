<?php

namespace App\Models;

use CodeIgniter\Model;

class PromptModel extends Model
{
    protected $table            = 'prompts';
   
    protected $allowedFields    = ['prompt','json_response','prompt_type','for_user_id'];
	
	public function get_latest_prompt($user_id,$type){ 
		return $this->where('prompt_type',$type)
					->where('for_user_id',$user_id)
					->limit(1)
					->orderBy('prompt_datetime','DESC')
		->findAll();
	}
	
	/** Turns a stored AI response into a reading, or null when the JSON is unusable. */
	public static function reading(array $row): ?array
	{
		$json = json_decode($row['json_response'] ?? '', true);
		if (! is_array($json) || empty($json['overview'])) {
			return null;
		}
		$overview = $json['overview'];
		return [
			'datetime'  => $row['prompt_datetime'],
			'date'      => date('M j, Y', strtotime($row['prompt_datetime'])),
			'overview'  => is_array($overview) ? implode(' ', $overview) : (string) $overview,
			'patterns'  => array_map('strval', (array) ($json['patterns'] ?? [])),
			'symbols'   => array_map('strval', (array) ($json['symbols'] ?? [])),
			'celestial' => array_map('strval', (array) ($json['significant_plantery_or_moons_syncing_with_dream_patterns'] ?? [])),
		];
	}

	public function get_user_prompts($user_id,$type,$limit){
		return $this->where('prompt_type',$type)
					->where('for_user_id',$user_id)
					->limit($limit)
					->orderBy('prompt_datetime','DESC')
		->findAll();
	}
	
}
