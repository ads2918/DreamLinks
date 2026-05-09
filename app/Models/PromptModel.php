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
	
	public function get_user_prompts($user_id,$type,$limit){ 
		return $this->where('prompt_type',$type)
					->where('for_user_id',$user_id)
					->limit($limit)
					->orderBy('prompt_datetime','DESC')
		->findAll();
	}
	
}
