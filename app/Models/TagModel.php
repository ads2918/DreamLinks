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
