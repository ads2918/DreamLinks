<?php

namespace App\Models;

use CodeIgniter\Model;

class BlockModel extends Model
{
    protected $table            = 'blocks';
   
    protected $allowedFields    = [
									'blocked_user_id',
									'blocked_by_user_id',
									'blocked_datetime',
									'disabled_flag',
									'disabled_datetime',
								];
	
	
	
	public function unblock($block_id,$blocked_by_user_id){   
		$results = $this->table('blocks')
					->where('id',$block_id)
					->where('blocked_by_user_id',$blocked_by_user_id)
					->set([
							'disabled_flag' => 1,
							'disabled_datetime' => date('Y-m-d h:i:s',strtotime('now'))
						  ])
					->update();
		return $results;
	}
	
	public function block_list($user_id_1){    
		$results = $this->table('blocks')
					->select('blocks.*,username,image')
					->join('users', 'users.id = blocked_user_id')
					->Where('blocked_by_user_id',$user_id_1)
					->where('disabled_flag',0)
					->limit(500)
					->Where('deleted',0)
					->findAll();
					
		return $results;
	}
	
	public function block_exsists($user_id_1,$user_id_2){     
		$results = $this->table('blocks')
					->select('blocks.*')
					->groupStart()
						->groupStart()
							->Where('blocked_by_user_id',$user_id_1)
							->Where('blocked_user_id',$user_id_2)
						->groupEnd()
						->orGroupStart()
							->Where('blocked_by_user_id',$user_id_2)
							->Where('blocked_user_id',$user_id_1)
						->groupEnd()
					->groupEnd()
					->where('disabled_flag',0)
					->findAll();
					
		return $results;
	}
 
}
