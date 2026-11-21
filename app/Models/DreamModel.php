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
	
	/* the feed: public dreams plus linked dreamers' "friends" dreams, newest first */
	public function get_front_page_dreams($limit = 0,$random = 0,$linked_friend_ids = array(),$current_user_id = false){
		return $this->visible_to((int) $current_user_id, $linked_friend_ids, null, false)
					->limit($limit)
					->findAll();
	}

	/**
	 * Starts a query for the dreams $viewer_id is allowed to see: public dreams, "friends" dreams
	 * by linked dreamers, and with $include_own every one of the viewer's own dreams.
	 * Leaves out deleted accounts and anyone either side has blocked.
	 * $owner_ids narrows it to those dreamers. Chain ->findAll() on the result.
	 */
	public function visible_to(int $viewer_id, array $linked_ids, ?array $owner_ids = null, bool $include_own = true){
		$linked_ids = array_map('intval', $linked_ids);
		$this->select('dreams.id, dreams.title, dreams.full_description, dreams.date, dreams.visibility, dreams.created_by, users.username, users.image')
			 ->join('users', 'users.id = dreams.created_by')
			 ->where('users.deleted', 0)
			 ->groupStart()
				->where('dreams.visibility', 'public');
		if(!empty($linked_ids)){
			$this->orGroupStart()
					->whereIn('dreams.created_by', $linked_ids)
					->where('dreams.visibility', 'friends')
				 ->groupEnd();
		}
		if($include_own){
			$this->orWhere('dreams.created_by', $viewer_id);
		}
		$this->groupEnd();
		// $viewer_id is typed int, so it is safe inside the raw subquery
		$this->where('NOT EXISTS (SELECT 1 FROM blocks b WHERE b.disabled_flag = 0 AND (
			(b.blocked_by_user_id = dreams.created_by AND b.blocked_user_id = ' . $viewer_id . ') OR
			(b.blocked_user_id = dreams.created_by AND b.blocked_by_user_id = ' . $viewer_id . ')))', null, false);
		if($owner_ids !== null){
			$this->whereIn('dreams.created_by', $owner_ids ? array_map('intval', $owner_ids) : [0]);
		}
		return $this->orderBy('dreams.date', 'DESC')->orderBy('dreams.id', 'DESC');
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
