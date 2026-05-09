<?php

namespace App\Models;

use CodeIgniter\Model;

class MoonCycleModel extends Model
{
    protected $table            = 'moon_cycles';
   
    protected $allowedFields    = [
									'cycle',
									'datetime'
								];
								
	public function exsits($date){  
		$results = $this->table('moon_cycles')
						->select('*')
						->where('DATE(datetime)',$date)
						->findAll();
 
		return $results;
	}
	
	/* queries links that are pending approval */
	public function next_cycle(){     
		$results = $this->table('moon_cycles')
						->select('*')
						->where('datetime <=','now()',FALSE)
						->orderBy('datetime', 'ASC')
						->limit(1)
						->findAll();
	
		return $results;
	}
 
}
