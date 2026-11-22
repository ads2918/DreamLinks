<?php

namespace App\Models;

use CodeIgniter\Model;

class TagModel extends Model
{
    protected $table            = 'tags';
   
    protected $allowedFields    = ['tag','dream_id'];
	
	public function delete_records_by_dream_id($id){ 
		$this->where('dream_id', $id); // Assuming 'id' is your primary key column
		$this->delete(); // Replace 'your_table_name' with your actual table name
		return $this->affectedrows(); 
	}
	
	public function tags_by_dream_id($id){ 
		return $this->where('dream_id', $id)->findAll(); // Assuming 'id' is your primary key column	 
	}
	
	/** Replaces a dream's tags. Tags are stored raw and escaped on output. */
	public function replace_for_dream(int $dream_id, array $tags){
		$this->delete_records_by_dream_id($dream_id);
		foreach($tags as $tag){
			$this->insert(['tag' => $tag, 'dream_id' => $dream_id]);
		}
	}

	/** Trims, shortens to 25 characters and de-duplicates tag strings. @return list<string> */
	public static function clean(array $values){
		$tags = [];
		foreach($values as $value){
			$value = trim(mb_substr(is_scalar($value) ? (string) $value : '', 0, 25));
			if($value !== ''){
				$tags[] = $value;
			}
		}
		return array_values(array_unique($tags));
	}

	/** @return array<int, list<string>> tags keyed by dream id */
	public function tags_for_dreams(array $dream_ids){
		$by_dream = [];
		if(empty($dream_ids)){
			return $by_dream;
		}
		foreach($this->whereIn('dream_id', array_map('intval', $dream_ids))->findAll() as $row){
			$by_dream[$row['dream_id']][] = $row['tag'];
		}
		return $by_dream;
	}

	public function top_10_tags($user_id){
		return $this->select('tag,count(*) as frequency')
			->join('dreams', 'tags.dream_id = dreams.id', 'left')
			->where('dreams.created_by', $user_id)
			->groupBy('tag')
			->orderBy('count(*)','DESC')
			->limit(10)
			->findAll(); // Assuming 'id' is your primary key column	 
	}
}
