<?php

namespace App\Controllers;
use App\Models\UserModel; 
use App\Models\DreamModel; 
use App\Models\LinkModel; 
use App\Models\TagModel; 
use App\Models\BlockModel; 
use App\Models\MoonCycleModel;
use App\Models\PromptModel;
use App\Models\ResonanceModel;
use App\Libraries\DreamFeed;
use App\Libraries\DreamSymbols;
use App\Libraries\MoonPhase;
use App\Libraries\ReadingRunner;
use App\Libraries\Readings;
use App\Libraries\Signup;
use App\Services\GeminiService;
use CodeIgniter\Config\Services;
class User extends BaseController
{
 
	public function index(): string{
		$data['title'] = ucfirst('Home'); 
		return view('templates/header', $data)
			. view('pages/home')
			. view('templates/footer');
	}
	
	public function register($error = 0){
		//if logged in redirect
		if(is_numeric($this->session->user_id)){
			return redirect()->to('/');
		}else{
			helper('form');
			$data['title'] = ucfirst('Register User'); 
			$data['error'] = $error;
			
			return view('templates/header', $data)
				. view('users/register')
				. view('templates/footer'); 
		}
	}
	
	public function admin(){
		$data['title'] = 'admin';
		return view('templates/header', $data)
			   . view('admin/admin')
			   . view('templates/footer'); 	
	}
 
	public function admin_user_approve($user_to_approve){
		$data['title'] = 'admin';
		$user_model = model(UserModel::class);
		$user_model->save([
			'id' => $user_to_approve,
			'status' => 'approved'
		]);
		$account = $user_model->get_record_by_id($user_to_approve); 
		$message = '<h1>Welcome to DreamLinks</h1>
		<p>Thank you for your intrest in dreamlinks. 
		Your access request for user <b>'. esc($account['username']) .'</b> has been approved.
		<a target="_blank" href="'.  site_url('/user/login') .'">Click here</a> to login.</p>Sincerely,<br>DreamLinks Team';
		$sent = $this->send_email($account['email'],'Welcome to DreamLinks',$message,1);
		return redirect()->to('admin/users');
	}
	
	public function admin_users(){
		$data['title'] = 'admin';
		$user_model = model(UserModel::class);
		$pending_user_list = $user_model->get_pending();
		$active_user_list = $user_model->get_active();
		$pending_user_list = $user_model->get_pending();
		$data['active_user_list'] = $active_user_list;
		$data['pending_user_list'] = $pending_user_list;
		$data['page_js'] = 'admin-users.js';
		return view('templates/header', $data)
			   . view('admin/users',$data)
			   . view('templates/footer'); 	
	}
	
	public function accountMenu($user_id){
		helper('url');
		helper('form');
		$session = \Config\Services::session();
		$data['title'] = ucfirst('Acount'); 
		$data['error'] = 0;
		$current_user_id = $session->user_id;
		if($current_user_id === $user_id){
			$model = model(UserModel::class);
			$account = $model->get_record_by_id($user_id);
			$data['account'] = $account;
			return view('templates/header', $data)
				. view('users/menuHeader.php',$data)
				. view('users/menu',$data)
				. view('templates/footer'); 
		}else{
			return redirect()->to('user/'. $current_user_id .'/account/');
		}
			
	}
	
	 public function generateText(string $prompt, string $model = 'gemini-1.5-flash'){
        $response = $this->client
            ->generativeModel($model) // Note: Use string model names (no enum in v2.0+)
            ->generateContent($prompt);

        return $response->text();
    }
 
	/** The journal: the user's own dreams as compact rows, with search, sort and filters (DreamModel::filtered). */
	public function dreams($user_id,$error = 0): string{
		$session = \Config\Services::session();
		$data['title'] = 'My Dreams';
		$data['error'] = $error;
		if($user_id != $session->user_id){
			session()->setFlashdata('error', 'You do have permissions to this page');
			return view('templates/header', $data)
			. view('templates/footer');
		}

		$viewer_id = (int) $session->user_id;
		$filters = array_map('strval', (array) $this->request->getGet(['q', 'since', 'sort', 'visibility'])) + ['q' => '', 'since' => '', 'sort' => '', 'visibility' => ''];
		$page = max(1, (int) $this->request->getGet('page'));
		$per_page = 20;
		// one extra row says whether there is an older page
		$rows = model(DreamModel::class)->visible_to($viewer_id, [], [$viewer_id], true, $filters)->findAll($per_page + 1, ($page - 1) * $per_page);

		$data['dreams'] = DreamFeed::decorate(array_slice($rows, 0, $per_page), $viewer_id);
		$data['has_more'] = count($rows) > $per_page;
		$data['page'] = $page;
		$data['filters'] = $filters;
		$data['page_js'] = 'journal.js';
		return view('templates/header', $data)
			. view('users/dreams', $data)
			. view('templates/footer', $data);
	}

	public function dream_analysis(){
		$data = Readings::forUser((int) $this->session->user_id, (bool) ($this->current_user['ai_analysis_enabled'] ?? false));
		$data['title'] = 'Readings';
		$data['page_js'] = 'analysis.js';

		return view('templates/header', $data)
			. view('users/analysis', $data)
			. view('templates/footer', $data);
	}

	/** POST readings/now : reads the user's waiting dreams right away; the Readings page spins until this answers */
	public function readNow(){
		$user_id = (int) $this->session->user_id;
		// Gemini takes a few seconds; don't hold the session lock and stall the user's other tabs
		session()->close();
		set_time_limit(90);
		$status = ($this->current_user['ai_analysis_enabled'] ?? false) ? ReadingRunner::readNow($user_id) : 'off';
		return $this->response->setJSON(['status' => $status]);
	}

	/** cron, every minute: reads new dreams, at most Config\Readings::$dailyCap readings per dreamer a day */
	public function aiUserDreamRun(){
		echo ReadingRunner::run() . PHP_EOL;
	}
	
	public function profile($user_id){
		$data['title'] = 'Profile';
		$session = \Config\Services::session();
		$user_model = model(UserModel::class);
		$profile_user = $user_model->get_record_by_id($user_id);
		if(empty($profile_user) || $profile_user['deleted']){
			return redirect('/');
		}
		$blocked = 0;
		$model = model(BlockModel::class);
		$block_exsists = $model->block_exsists($user_id,$session->user_id);
		if($block_exsists){
			$blocked = 1;
			$block_record = $model;
			if(!empty($block_record)){
				if($block_exsists[0]['blocked_by_user_id'] != $session->user_id){
					return redirect()->to('/');
				}
			}
		}
		
		$dream_model = model(DreamModel::class);
 
		$link = model(LinkModel::class);
		$link_pending = 0;
		$link_approved = 0;
		$link_exsists = $link->link_request_pending($user_id,$session->user_id);
		if($link_exsists){
			if($link_exsists[0]['decline_flag'] == 0 && !$link_exsists[0]['request_approved'] && !$blocked){
				$link_pending = 1;
			}elseif($link_exsists[0]['decline_flag'] == 0 && $link_exsists[0]['request_approved'] && !$blocked){
				$link_approved = 1;
			}
		}
		
		$viewer_id = (int) $session->user_id;
		$recent_dreams = $dream_model->visible_to($viewer_id, model(LinkModel::class)->linked_user_ids($viewer_id), [(int) $user_id])->findAll(50);
		if((int) $user_id === $viewer_id){
			// your own profile shows what others can see, not your private dreams
			$recent_dreams = array_values(array_filter($recent_dreams, fn ($d) => $d['visibility'] !== 'private'));
		}
		$recent_dreams = DreamFeed::decorate($recent_dreams, $viewer_id);

		$data['profile_user'] = $profile_user;
		$data['page_js'] = 'profile.js';
		$data['blocked'] = $blocked;
		$data['link_pending'] = $link_pending;
		$data['link_approved'] = $link_approved;
		$data['session_user'] = $session->user_id;
		$data['recent_dreams'] = $recent_dreams;
		return view('templates/header', $data)  
			   . view('users/profile',$data)
			   . view('templates/footer',$data); 
	}
	
	public function blocks($user_id){
		$data['title'] = 'Blocks';
		$session = \Config\Services::session();
		if($session->user_id == $user_id){
			$block = model(BlockModel::class);
			$block_list = $block->block_list($user_id);
			$data['block_list'] = $block_list;
			$model = model(UserModel::class);
			$account = $model->get_record_by_id($user_id);
			$data['account'] = $account;
			$data['page_js'] = 'blockTable.js';
		return view('templates/header', $data)  
			   . view('users/menuHeader.php',$data)
			   . view('users/blocks',$data)
			   . view('templates/footer',$data); 
		}else{
			return redirect()->to('user/'. $session->user_id .'/blocks');
		}
			
	}
	
	public function links($user_id){
		$model = model(UserModel::class);
		$account = $model->get_record_by_id($user_id);
		$data['account'] = $account;
		$session = \Config\Services::session();
		if($session->user_id == $user_id){
			$link = model(LinkModel::Class);
			$links = $link->user_approved_links($user_id);
			$data['title'] = 'Links'; 
			$data['links'] = $links;
			$data['page_js'] = 'linkTable.js';
			return view('templates/header', $data) 
				   . view('users/menuHeader.php',$data)
				   . view('users/links',$data)
				   . view('templates/footer',$data); 
		}else{
			return redirect()->to('user/'. $session->user_id .'/links/');
		}	
	}
	
	public function dreamsTable($user_id){
		helper('url');
		$session = \Config\Services::session();
		if($user_id != $session->user_id){
			 return false;
		}else{
			$search = '';
			if(isset($_POST['search']['value']) && $_POST['search']['value'] != ''){
				$search = $_POST['search']['value'];
			}
 
			$start = '' ? 0 : (int)$_POST['start'];
			$length = '' ? 10 : (int)$_POST['length'];
			$total_val = 0;
			$columnIndex = $_POST['order'][0]['column']; // Column index to sort by
			$columnName = $_POST['columns'][$columnIndex]['data']; // Column name (from client-side 'data' property)
			$columnSortOrder = $_POST['order'][0]['dir']; // 'asc' or 'desc'
			if($length > 100){
				$length = 100;
			}
			
			if(!in_array($columnName,array('full_description','date','title','visibility'))){
				$columnName = 'visibility';
			}
			
			if(strtoupper($columnSortOrder) != 'ASC' && strtoupper($columnSortOrder) != 'DESC'){
				$columnSortOrder = 'ASC';
			}
			
			$model = model(DreamModel::class);
			$total_records_val = 0;
			$total_records = $model->get_records_by_user($user_id,'','all');
			$dreams = $model->get_records_by_user($user_id,$search,$start,$length,$columnName,$columnSortOrder);
			if(!empty($dreams)){
				foreach($dreams as $key=>$dream){
					$tag_model = model(TagModel::class);
					$tags = $tag_model->tags_by_dream_id($dream['id']);
					$tag_list = array();
					if(!empty($tags)){
						foreach($tags as $tag_row){
							$tag_list[] = '<span class="badge badge-dark">'. esc($tag_row['tag']) .'</span>';
						}
					}
					// DataTables renders these as HTML, so every user-written value is escaped here
					$dreams[$key]['date'] = date('m/d/Y',strtotime($dream['date']));
					$dreams[$key]['title'] = esc($dream['title']);
					$dreams[$key]['full_description'] = esc(mb_substr($dream['full_description'],0,50)) .'...';
					unset($dreams[$key]['slug']);
					unset($dreams[$key]['created_by']);
					unset($dreams[$key]['created_datetime']);
					$dreams[$key]['tags'] = implode(' ',$tag_list);
					$dreams[$key]['links'] = '<a href="'. site_url('dream/edit/'. $dream['id']) .'">
							<i class="bi bi-pencil-square"></i>
						</a> 
						<span data-toggle="modal" data-target="#exampleModal" title="'. esc($dream['title'], 'attr') .'" class="delete-dream" href="'. site_url('dream/delete/'. $dream['id']) .'">
							<i class="bi bi-trash"></i>
						</span>';
					
					
				}
				$total_val = count($dreams);
				$total_records_val = count($total_records);
			}
			
			$draw = $_POST['draw'];
			if($search == ''){
				$total_val = $total_records_val;
			}
			echo json_encode([
				'draw' => $draw,
				'recordsTotal' => $total_records_val,
				'recordsFiltered' => $total_val,
				'data' => $dreams,
				'start' => $start,
			]);
		}
			
	}
	
	public function searchTable(){
		helper('url');
		$session = \Config\Services::session();
		 
		$search = '';
		if(isset($_POST['search']['value']) && $_POST['search']['value'] != ''){
			$search = $_POST['search']['value'];
		}
		$users_found_html = array();
		$users_found = array();
		$total_users_found = array();
		$total_records_val = 0;
		$start = '' ? 0 : (int)$_POST['start'];
		$length = '' ? 10 : (int)$_POST['length'];
		$total_val = 0;
		$columnIndex = 0; // Column index to sort by
		$columnName = 'username'; // Column name (from client-side 'data' property)
		$columnSortOrder = $_POST['order'][0]['dir']; // 'asc' or 'desc'
		if($length > 100){
			$length = 100;
		}
		if(strtoupper($columnSortOrder) != 'ASC' && strtoupper($columnSortOrder) != 'DESC'){
			$columnSortOrder = 'ASC';
		}
		
		if(isset($_GET['search']) && $_GET['search'] != ''){
			$model = model(UserModel::class);	
			$link = model(LinkModel::class);	
			$block = model(BlockModel::class);
			$total_users_found = $model->find_usernames_like(esc($_GET['search']),'all','',$columnName,$columnSortOrder);
			$users_found = $model->find_usernames_like(esc($_GET['search']),$start,$length,$columnName,$columnSortOrder);
			foreach($users_found as $key=>$user_record){				
				$link_exsists = $link->link_request_pending($user_record['id'],$session->user_id);
				$block_exsists = $block->block_exsists($user_record['id'],$session->user_id);
				if(!empty($block_exsists)){
					// if($block_exsists[0]['blocked_by_user_id'] != $session->user_id){
						unset($users_found[$key]);
						continue;
					// }
				}
				if($link_exsists){
					foreach($link_exsists as $link2){
						if($block_exsists ){//remove entry if flagged as block by either party requestor or recipient
							unset($users_found[$key]);
						}elseif($link2['request_approved']){
							$users_found[$key]['link_approved'] = 1;
						}elseif(!$link2['decline_flag'] && !$link2['request_approved']){
							$users_found[$key]['link_approved'] = 0;
							$users_found[$key]['link_pending'] = 1;
						}else{
							$users_found[$key]['link_pending'] = 0;
							$users_found[$key]['link_approved'] = 0;
						}
					}
				}else{
					$users_found[$key]['link_pending'] = 0;
					$users_found[$key]['link_approved'] = 0;
				}
			}
		}
		
		if($users_found){
			foreach($users_found as $user_record){
				$profile_image = '';
				if($user_record['image'] == ''){ 
					$profile_image =  base_url('assets/images/no-image.png'); 
				}else{ 
					$profile_image =  base_url('uploads/'. $user_record['image']); 
				} 
				$html = '';
				$html .= '<a href="'. site_url('/user/'. (int) $user_record['id']) .'" ><img class="profile-image"  src="'. esc($profile_image, 'attr') . '"></a>
						<div>
							'. esc($user_record['username']) .'
						</div>
						<div>';
				if($session->user_id != $user_record['id']){
					if($user_record['link_approved']){
						$html .= '<i title="Linked" class="bi bi-link-45deg larger-icon"></i></i>';
					}elseif($user_record['link_pending']){
						$html .='<i title="Link request pending" title="Link request pending" class="bi bi-clock larger-icon link-pending"></i>';
					}else{
						$html .= '<span title="Request Link" class=\'request-link\' link_id="'. (int) $user_record['id'] .'">
							<i class="bi bi-patch-plus-fill larger-icon"></i>
						</span>';
					}
				}else{
					$html .= '<strong>You</strong>';
				}
				$html .= '</div>';
				$total_records_val++;
				$users_found_html[] = array('username' => $html);
			}
		}
		$draw = $_POST['draw'];
 
		echo json_encode([
			'draw' => $draw,
			'recordsTotal' => count($total_users_found),
			'recordsFiltered' => count($total_users_found),
			'data' => $users_found_html,
			'start' => $start,
		]);
 
	}
	
	public function search(){
		$data['title'] = 'Search';
 
		$data['page_js'] = 'linkSearch.js';
		$data['searched_for'] =  esc($_GET['search']);
 
		return view('templates/header', $data)
				. view('users/search', $data)
				. view('templates/footer'); 
	}
 
	
	 public function create(){
		/* logged in redirect */
		if($this->logged_in()){
			return redirect()->to('/');
		}
        helper('form');
		$data2['title'] = ucfirst('Register'); 
        $data = $this->request->getPost(['email', 'password','email','password2','username']);
		
        // Checks whether the submitted data passed the validation rules.
        if (! $this->validateData($data, [
             'username'  => 'required|max_length[50]|min_length[3]|is_unique[users.username]',
			'email'     => 'required|max_length[50]|valid_email|is_unique[users.email]',
			'password'  => 'required|max_length[30]|min_length[8]|matches[password2]',
			'password2' => 'required',
        ])){
            // The validation fails, so returns the form.
            return $this->register(1);
        }
	
 
		$done = 'Almost there! We sent a link to your email. Open it to confirm your account, then log in.';
		// bots fill in the hidden "website" field; act as if it worked so they don't adapt
		if ((string) $this->request->getPost('website') !== '') {
			session()->setFlashdata('success', $done);
			return redirect()->to('user/login');
		}
		if (! service('throttler')->check('signup-' . md5($this->request->getIPAddress()), 5, HOUR)) {
			session()->setFlashdata('error', 'Too many sign-ups from here. Try again in an hour.');
			return redirect()->to('user/register');
		}

        // Gets the validated data.
        $post = $this->validator->getValidated();
        $model = model(UserModel::class);
		$hashed_password = password_hash($post['password'], PASSWORD_DEFAULT);
        $model->save([
            'password'  => $hashed_password,
			'email' => $post['email'],
            'username' => $post['username'],
			'status' => 'unconfirmed',
        ]);
		Signup::sendConfirmation(['id' => $model->getInsertID(), 'email' => $post['email'], 'username' => $post['username']]);
		$this->send_email($this->admin_email, 'DreamLinks - new sign-up', esc($post['username']) . ' just signed up.');

		session()->setFlashdata('success', $done);
		return redirect()->to('user/login');
    }

	/** GET user/confirm/{token}: the link in the sign-up email */
	public function confirm($token = ''){
		if (! Signup::confirm((string) $token)) {
			session()->setFlashdata('error', 'That link has expired or was already used. Log in and we will send you a new one.');
		} else {
			session()->setFlashdata('success', 'Your email is confirmed. Welcome to DreamLinks! Log in to start your journal.');
		}
		return redirect()->to($this->logged_in() ? '/' : 'user/login');
	}
	
	public function login(){
		helper('form');
		$data2['title'] = 'Login - Sign In to Your Account';
		/* logged in redirect */
		if($this->logged_in()){
			return redirect()->to('/');
		}
		return view('templates/header', $data2)
			. view('users/login')
			. view('templates/footer'); 
	}
	
	public function edit($error = 0){
		helper('form');
		$session = \Config\Services::session();
		$model = model(UserModel::class);
		$data = $model->get_record_by_id($session->user_id);
		$data['account'] =  $data;
		$data['title'] = 'User Edit';
		$data['error'] = $error;	
		return view('templates/header', $data)
			. view('users/menuHeader.php',$data)
			. view('users/edit', $data)
			. view('templates/footer'); 
	}
	
	public function resetPassword(){
		$data['title'] = 'Reset Password';
		return view('templates/header', $data)
			. view('users/forget-password.php',$data)
			. view('templates/footer'); 
	}
	
	public function editSubmit(){
		helper('form');
		$error = 0;
		$session = \Config\Services::session();
		$data = $this->request->getPost(['email', 'current_password','email','new_password','username','profile_image','bio','ai_analysis_enabled','default_dream_visibility','reminder_enabled','reminder_hour','timezone']);

        if (!$this->validateData($data, [
			'new_password'  => 'max_length[30]',
			'email'  => 'required|max_length[50]|valid_email|min_length[10]|is_unique[users.email,id,'. $session->user_id .']',
			'username'  => 'max_length[50]|min_length[4]|is_unique[users.username,id,'. $session->user_id .']',
			'bio' => 'max_length[200]|min_length[1]',
			'default_dream_visibility' => 'required|in_list[private,friends,public]',
			'reminder_hour' => 'permit_empty|integer|greater_than_equal_to[4]|less_than_equal_to[12]',
			'image' => 'is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]|max_size[image,100]',
        ])){
			return $this->edit(1);
		}
		$timezone = in_array($data['timezone'] ?? '', \DateTimeZone::listIdentifiers(), true) ? $data['timezone'] : 'America/Indianapolis';
		$upload_path = $this->upload_path;
		$file_name = '';	
		$model = model(UserModel::class);
		$user = $model->get_record_by_id($session->user_id);	
		$reset_password = 0;
		if($data['current_password'] != '' && $data['new_password']){
			if($user && !password_verify($data['current_password'], $user['password'])) {
				$error = 1;
				session()->setFlashdata('error', 'Current password is invalid.'); 
			}else{
				$reset_password = 1;
			}
		}
		
		$ai_analysis = 0;
		if($data['ai_analysis_enabled']){
			$ai_analysis = 1;
		}
		
		$update_image = 0;
		$img = $this->request->getFile('image');
		if($img && $img->isValid() && !$img->hasMoved()) {
			// getMimeType() reads the file itself; $_FILES['type'] is whatever the browser claims
			$file_type = $img->getMimeType();
			$file_size = $img->getSize();
			$file_tmp_name = $img->getTempName();
			if(in_array($file_type,array('image/jpeg','image/png'), true) && @getimagesize($file_tmp_name)){
				if($file_size <= 2097152){
					$newName = $img->getRandomName();
					$file_name = $newName  . '.jpg';
					$copy = move_uploaded_file($file_tmp_name,$upload_path . $file_name);
					if(!$copy){
						$error = 1;
						session()->setFlashdata('error', 'File could not be copied to upload directory.'); 
					}else{
						$update_image = 1;
					}
				}else{
					$error = 1;
					session()->setFlashdata('error', 'File must be 2MB or under.');
				}
			}else{
				$error = 1;
				session()->setFlashdata('error', 'File must be a jpg or png.');
			}
		}
	 
		if($error){
			return $this->edit(1);
		}else{
			$model = model(UserModel::class);
			$fields = [
				'id' => $session->user_id,
				'username' => $data['username'],
				'bio' => $data['bio'],
				'ai_analysis_enabled' => $ai_analysis,
				'default_dream_visibility' => $data['default_dream_visibility'],
				'reminder_enabled' => $data['reminder_enabled'] ? 1 : 0,
				'reminder_hour' => (int) ($data['reminder_hour'] ?: 8),
				'timezone' => $timezone,
			];
			if($reset_password){
				$fields['password'] = password_hash($data['new_password'], PASSWORD_DEFAULT);
			}
			$model->save($fields);
			
			if($update_image){
				//remove original image if one was linked previously
				if($user['image'] != ''){
					if(file_exists($upload_path . $user['image'])){
						unlink($upload_path . $user['image']);
					}
				}
				$model->save([
					'id' => $session->user_id,
					'image'  => $file_name,
				]);
			}
			session()->setFlashdata('success', 'User account has been updated');
			return redirect()->to('user/'. $user['id'] .'/account'); // Redirect to login page or desired location
		}
	}
	
	public function logout(){
		if($this->session->user_id){
			$session = \Config\Services::session();
			$session->destroy(); // Destroys the entire session
			return redirect()->to('user/login'); // Redirect to login page or desired location
		}
	}
	
	
	public function loginSubmit() {
		/* logged in redirect */
		if($this->logged_in()){
			return redirect()->to('/');
		}
		helper(['form']);
		$model = model(UserModel::class);

		$rules = [
			'email'    => 'required|valid_email',
			'password' => 'required',
		];

		if (!$this->validate($rules)) {
			return view('templates/header', ['title' => 'Login'])
				 . view('users/login')
				 . view('templates/footer');
		}

		// 10 tries a minute per address, like the app's sign-in
		if (! service('throttler')->check('web-login-' . md5($this->request->getIPAddress()), 10, MINUTE)) {
			session()->setFlashdata('error', 'Too many tries. Wait a minute and try again.');
			return redirect()->back()->withInput();
		}

		$email = $this->request->getPost('email');
		$password = $this->request->getPost('password');

		// 1. Find user by email ONLY first
		$user = $model->where('email', $email)->first();
		if ($user && password_verify($password, $user['password']) && !$user['deleted']) {

			// 2. The email has to be confirmed first; the right password earns a fresh link
			if (in_array($user['status'], Signup::UNCONFIRMED, true)) {
				Signup::sendConfirmation($user);
				session()->setFlashdata('error', 'Confirm your email first. We just sent a new link to ' . $user['email'] . '.');
				return redirect()->back()->withInput();
			}
			if ($user['status'] !== 'approved') {
				session()->setFlashdata('error', 'This account can\'t log in. Use the contact page if you think that is a mistake.');
				return redirect()->back()->withInput();
			}

			// 3. Success! New session id so a session id planted before login is useless
			session()->regenerate(true);
			$user_data = [
				'user_id'   => $user['id'],
				'email'     => $user['email'],
				'role'      => $user['role'],
				'logged_in' => true
			];
			session()->set($user_data);
			// return redirect()->to(base_url('dashboard')); // Better to specify a destination
			return redirect()->to('');
		} else {
			// Generic error for security (don't reveal if email exists or not)
			session()->setFlashdata('error', 'Invalid email or password.');
			return redirect()->back()->withInput();
		}
	}
	// public function loginSubmit(){
		// helper('form');
		// $data2['title'] = 'Login';
		
        // $data = $this->request->getPost(['email', 'password']);
		// if (!$this->validateData($data, [
            // 'email' => 'required',
            // 'password'  => 'required',
        // ])){
          // return view('templates/header', $data2)
			// . view('users/login')
			// . view('templates/footer'); 
        // }
		
		// if($data){
			// $post = $this->validator->getValidated();
			// $model = model(UserModel::class);
			// $user = $model->where('email',$post['email'])->where('status','approved')->first();
			// if(!empty($post)){
				// if($user && password_verify($post['password'], $user['password'])) {
					// $user_data = [
						// 'user_id' => $user['id'],
						// 'email' => $user['email'],
						// 'role' => $user['role'],
						// 'logged_in' => TRUE
					// ];
					// session()->set($user_data);
					// return redirect()->to('');
				// }else{
					// session()->setFlashdata('error', 'Invalid username or password.');
					// return redirect()->to('user/login'); // Redirect to login page or desired location
				// }
			// }
		// }	
	// }
}
