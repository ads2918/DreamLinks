<?php

namespace App\Models;

use CodeIgniter\Model;

class LinkModel extends Model
{
    protected $table            = 'links';
   
    protected $allowedFields    = [
									'request_approved',
									'request_user_id',
									'request_datetime',
									'recipient_user_id',
									'request_approved_datetime',
									'decline_flag',
									'decline_datetime'
								];
	
	/* queries links that are pending approval */
	public function user_pending_links($recipient_id){     
		$results = $this->table('links')
						->select('links.*, image,username')
						->join('users', 'users.id = links.request_user_id', 'left')
						->where('recipient_user_id',$recipient_id)
						->where('request_approved',0)
						->where('decline_flag',0)->findAll();
		return $results;
	}
	
	/* queries all links between two users */
	public function link_request_pending($user_id_1,$user_id_2){     
		$results = $this->table('links')
					->select('links.*')
					->groupStart()
						->groupStart()
							->Where('request_user_id',$user_id_1)
							->Where('recipient_user_id',$user_id_2)
						->groupEnd()
						->orGroupStart()
							->Where('request_user_id',$user_id_2)
							->Where('recipient_user_id',$user_id_1)
						->groupEnd()
					->groupEnd()
					->where('decline_flag',0)
					->findAll();
					
		return $results;
	}
	
	/* queries all links approved between two users */
	public function user_approved_links($user_id_1){  
		$caseStatement = "users.id = CASE WHEN links.request_user_id != '". $user_id_1 ."' THEN links.request_user_id ELSE links.recipient_user_id END";
		$results = $this->table('links')
					->select('links.*,username,image,users.id as profile_id')
					->join('users', $caseStatement, 'left') // Or 'inner', 'right
					->groupStart()
						->groupStart()
							->Where('request_user_id',$user_id_1)
						->groupEnd()
						->orGroupStart()
							->Where('recipient_user_id',$user_id_1)
						->groupEnd()
					->groupEnd()
					->where('request_approved',1)
					->where('decline_flag',0)
					->where('deleted',0)
					->findAll();
								
		return $results;
	}
	
	/* sets all pending links or current links to decline state */
	public function decline_links($user_id_1,$user_id_2){
		return $this->groupStart()
						->groupStart()
							->Where('request_user_id',$user_id_1)
							->Where('recipient_user_id',$user_id_2)
						->groupEnd()
						->orGroupStart()
							->Where('request_user_id',$user_id_2)
							->Where('recipient_user_id',$user_id_1)
						->groupEnd()
						->Where('decline_flag',0)
					->groupEnd()
                    ->set(['decline_flag' => 1,'decline_datetime' => date('Y-m-d h:m:s',strtotime('now'))]) // Data to update
                    ->update(); // Execute the update
	}
	
	public function decline_link_by_id($link_id){
		return $this->Where('id',$link_id) 
					->Where('decline_flag',0)
                    ->set(['decline_flag' => 1,'decline_datetime' => date('Y-m-d h:m:s',strtotime('now'))]) // Data to update
                    ->update(); // Execute the update
	}
}
