<?php

namespace App\Controllers;
use App\Models\DreamModel; 
use App\Models\TagModel; 
class Dream extends BaseController
{
 
	public function index(): string{
		// $data['title'] = ucfirst('Home'); 
		// return view('templates/header', $data)
			// . view('pages/home')
			// . view('templates/footer');
	}
	
	public function editSubmit($id){
	    helper('form');
		helper('text'); // For convert_accented_characters
		helper('url');  // For url_title
		$data = $this->request->getPost(['date', 'title','full_description','tags','visibility']);
		$session = \Config\Services::session();
		$dream_model = model(DreamModel::class);	
		$dream_model = $dream_model->find($id);
		if($dream_model['created_by'] != $this->current_user['id']){
			return redirect()->to('');
		}
		if (! $this->validateData($data, [
            'title'  => 'required|max_length[100]|min_length[1]',
			'full_description'  => 'required|trim|max_length[700]|min_length[1]',
			'date'  => 'trim|required|valid_date',
			'tags' => 'required',
			'visibility' => 'required|in_list[public,private,friends]',
        ])){
            // The validation fails, so returns the form.
            return $this->edit($id,1);
        }

		$tags = json_decode($data['tags']);
		if(count($tags) > 15){  
			session()->setFlashdata('error', 'Cannot use more than 15 tags.');
			return $this->edit($id);
		}
			
		$post = $this->validator->getValidated(); 
		$string = $post['title'];
		$model = model(DreamModel::class);
		$model->save([
			'id' =>  $id,
			'date'	=> $this->request->getPost('date'),
			'title'	=> $this->request->getPost('title'), // Save raw, escape on output
			'full_description' => $this->request->getPost('full_description'),
			'visibility' => $this->request->getPost('visibility'),
        ]);	
		
		/* save update tags */
		$model = model(TagModel::class);
		$model->delete_records_by_dream_id($id);
		if(!empty($tags)){
			foreach($tags as $tag){
				$model->save([
					'tag' => substr($tag->value,0,25),
					'dream_id' => $id,
				]);	
			}
		}
		
		$data['error'] = 0;
		session()->setFlashdata('success', 'Dream has been updated.');
		return redirect()->to('../user/'. $session->user_id .'/dreams');
	}
	
	public function edit($id,$error = 0){
		helper('form');
		$model = model(DreamModel::class);
		$data = $model->find($id);
		if($data['created_by'] != $this->current_user['id']){
			return redirect()->to('');
		}
		$header_data['title'] = ucfirst('Edit Dream'); 
		$data['error'] = $error;				
		$tag_arr = array();
		if(!isset($_POST['tags'])){	
			$tag_model = model(TagModel::class);
			$tags = $tag_model->tags_by_dream_id($id);
			$tag_arr = array();
			foreach($tags as $tag){
				$tag_arr[] = $tag['tag'];
			}
		}elseif(isset($_POST['tags'])){
			$tags = json_decode($_POST['tags']);
			if(!empty($tags)){
				foreach($tags as $tag){
					$tag_arr[] = $tag->value;
				}
			}
		}
	 
		// $data['visibility'] = $data['visibility'];
		$data['tags'] = implode(',',$tag_arr);
		return view('templates/header',$header_data)
			. view('dreams/add-edit',$data)
			. view('templates/footer');
	}
	
	public function delete(?int $id = null){
		$deleted = false;
		$session = \Config\Services::session();
		$model = model(DreamModel::class);
		$data = $model->find($id);
		if($data['created_by'] == $session->user_id){
			$model2 = model(DreamModel::class);
			$deleted = $model2->delete_record_by_id($id);
		}
		if($deleted){
			session()->setFlashdata('success', 'Dream has been deleted.');
		}else{
			session()->setFlashdata('error', 'Dream could not be deleted.');
		}
		return redirect()->to('../user/'. $data['created_by'] .'/dreams');
	}
	
	public function add($error = 0){
		helper('form');
		$data['title'] = ucfirst('Add Dream'); 
		$data['error'] = $error;
		$data['visibility'] = '';
		if(!isset($tags)){
			$data['tags'] = '';
		}
		return view('templates/header', $data)
			. view('dreams/add-edit')
			. view('templates/footer');

    }
	
	public function addSubmit(){
	    helper('form');
		helper('text'); // For convert_accented_characters
		helper('url');  // For url_title
		$data = $this->request->getPost(['date', 'title','full_description','tags','visibility']);
		$session = \Config\Services::session();
		
		if (! $this->validateData($data, [
            'title'  => 'required|max_length[100]|min_length[1]',
			'full_description'  => 'required|trim|max_length[700]|min_length[1]',
			'date'  => 'trim|required|valid_date',
			'tags' => 'required',
			'visibility' => 'required|in_list[public,private,friends]',
        ])){
            // The validation fails, so returns the form.
            return $this->add(1);
        }
 
		$post = $this->validator->getValidated(); 
		$string = $post['title'];
		$model = model(DreamModel::class);
		$model->save([
			'created_by' => $session->user_id,
			'date'	=> $this->request->getPost('date'),
			'title'	=> $this->request->getPost('title'), // Save raw, escape on output
			'full_description' => $this->request->getPost('full_description'),
			'visibility' => $this->request->getPost('visibility'),
        ]);	
		
		$dream_id = $model->insertID;
		$tags = json_decode($data['tags']);
		if(count($tags) > 15){
			session()->setFlashdata('error', 'Cannot use more than 15 tags.');
            return $this->edit(1);
		}
		
		/* save update tags */
		$dream_tag = model(TagModel::class);
		if(!empty($tags)){
			foreach($tags as $tag){
				$dream_tag->save([
					'tag' => esc($tag->value),
					'dream_id' => $dream_id,
				]);	
			}
		}
		
		$data['error'] = 0;
		session()->setFlashdata('success', 'Dream has been logged.');
		return redirect()->to('../user/'. $session->user_id .'/dreams');
	}
}
