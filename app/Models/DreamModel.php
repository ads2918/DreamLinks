<?php

namespace App\Models;

use CodeIgniter\Model;

class DreamModel extends Model
{
    protected $table            = 'dreams';
   
    protected $allowedFields    = ['created_by','date','full_description','title','visibility','ai_prompt_id'];
 
	public function get_records_by_user($user_id,$search = '',$start=0,$length=10,$column ='date' ,$order ='ASC') {
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
	
	public function get_front_page_dreams($limit = 0,$random = 0,$linked_friend_ids = array(),$current_user_id = false){
 
		if(empty($linked_friend_ids)){
			$this->groupStart();
				$this->Where('visibility','public');
			$this->groupEnd();
		}else{
			$this->groupStart();
				$this->groupStart();	
					$this->whereIn('created_by',$linked_friend_ids);
					$this->where('visibility','friends');
				$this->groupEnd();
				$this->orGroupStart();
					$this->orWhere('visibility','public');
				$this->groupEnd();	
			$this->groupEnd();
		}
 
		$this->where("(SELECT count(*) FROM blocks WHERE 
		((blocked_by_user_id = created_by AND blocked_user_id = ". $current_user_id .") OR (blocked_user_id = created_by AND blocked_by_user_id = ". $current_user_id .")) AND disabled_flag = 0) = 0", NULL, FALSE);
		
		if($random){
		   $this->orderBy('id', 'RANDOM');
		}
		$this->join('users', 'users.id = created_by', 'left');
		$this->where('deleted',0);
		$this->limit($limit);
		return $this->findAll();	
		
	}
	
	public function get_dreams_by_status($user_id = 0,$status= 'public',$limit = 0,$random = 0){
		if(is_array($status)){
			$this->whereIn('visibility',$status);
		}else{
			$this->where('visibility',$status);
		}
		$this->where('created_by', $user_id);
		if($random){
		   $this->orderBy('id', 'RANDOM');
		}
		$this->join('users', 'users.id = created_by', 'left');
		$this->limit($limit);
		return $this->findAll();				
	}
	
	public function get_unprompted_dreams_by_user_id($user_id) {
		return $this->where('created_by', $user_id)
			   ->where('ai_prompt_id',0)
			   ->findAll();				
	}
	 
	public function delete_record_by_id($id){ 
		$this->where('id', $id); // Assuming 'id' is your primary key column
		$this->delete(); // Replace 'your_table_name' with your actual table name
		return $this->affectedrows(); 
	}
	

}
