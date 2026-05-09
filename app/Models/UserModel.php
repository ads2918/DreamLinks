<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
   
    protected $allowedFields    = [
									'username',
									'password',
									'email',
									'image',
									'bio',
									'status',
									'ai_analysis_enabled',
									'default_dream_visibility',
									'reset_token',  
									'reset_expires' 
								];
	
	public function delete_record_by_id($id){ 
		$this->where('id', $id); // Assuming 'id' is your primary key column
		$this->delete(); // Replace 'your_table_name' with your actual table name
		return $this->affectedrows(); 
	}
	
	public function get_active(){ 
		return $this->where('deleted', 0)  
		->where('status','approved') 
		->findAll();
	}
	
	public function get_pending(){ 
		return $this->where('deleted', 0)
				->where('status','pending_approval') 
		->findAll();
	}
 
	public function find_usernames_like($partial_user_name,$start=0,$length=10,$column ='username',$order ='ASC'){
		if($start == 'all'){
			return $this->like('username',$partial_user_name)
			->orderBy($column,$order)
			->where('deleted',0)
			->findAll();
		}else{
			return $this->like('username',$partial_user_name)
			->where('deleted',0)
			->limit($length,$start)
			->orderBy($column,$order)
			->findAll();
		}
 
	}
	
	public function get_record_by_id($id){ 
		return $this->find($id); // Assuming 'id' is your primary key column
	}
	
	public function search($search = '',$start=0,$length=10,$column ='date',$order ='ASC') {
		if($search == ''){
			if($start == 'all'){
				return $this->where('created_by', $user_id)
							->orderBy($column,$order)->findAll();
			}else{
				return $this->limit($length,$start) 
							->where('created_by', $user_id)
							->orderBy($column,$order)->findAll();
			}
		}elseif($search != ''){
			$search = strip_tags(htmlentities($search));
			return $this->groupStart()
						->like('title',$search)
						->orLike('full_description',$search)
						->groupEnd()
						->limit($length,$start)
						->where('created_by', $user_id)
						->orderBy($column,$order)
						->findAll();
		}
    }
	 
}
