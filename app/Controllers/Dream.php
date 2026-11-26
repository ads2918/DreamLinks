<?php

namespace App\Controllers;
use App\Models\DreamModel;
use App\Models\TagModel;
use App\Models\LinkModel;
use App\Models\ResonanceModel;
use App\Libraries\DreamSymbols;
class Dream extends BaseController
{
	private const RULES = DreamModel::RULES + ['tags' => 'required'];

	public function editSubmit($id){
	    helper('form');
		$data = $this->request->getPost(['date', 'title','full_description','tags','visibility']);
		$dream = model(DreamModel::class)->find($id);
		if(!$dream || $dream['created_by'] != $this->current_user['id']){
			return redirect()->to('');
		}
		if (! $this->validateData($data, self::RULES)){
            return $this->edit($id,1);
        }

		$tags = $this->parse_tags($data['tags']);
		if(count($tags) > DreamModel::MAX_TAGS){
			session()->setFlashdata('error', 'Cannot use more than 15 tags.');
			return $this->edit($id,1);
		}

		$model = model(DreamModel::class);
		$model->save([
			'id' =>  $id,
			'date'	=> $data['date'],
			'title'	=> $data['title'], // Save raw, escape on output
			'full_description' => $data['full_description'],
			'visibility' => $data['visibility'],
        ]);
		$this->save_tags($id, $tags);

		session()->setFlashdata('success', 'Dream has been updated.');
		return redirect()->to('user/'. $this->session->user_id .'/dreams');
	}

	public function edit($id,$error = 0){
		helper('form');
		$model = model(DreamModel::class);
		$data = $model->find($id);
		if(!$data || $data['created_by'] != $this->current_user['id']){
			return redirect()->to('');
		}
		$header_data['title'] = 'Edit Dream';
		$header_data['page_js'] = 'dream-form.js';
		$data['error'] = $error;
		$tag_arr = array();
		if($this->request->getPost('tags') === null){
			$tag_model = model(TagModel::class);
			foreach($tag_model->tags_by_dream_id($id) as $tag){
				$tag_arr[] = $tag['tag'];
			}
		}else{
			$tag_arr = $this->parse_tags($this->request->getPost('tags'));
		}
		$data['tags'] = implode(',',$tag_arr);
		$data['lexicon'] = DreamSymbols::toJson();
		return view('templates/header',$header_data)
			. view('dreams/add-edit',$data)
			. view('templates/footer',$header_data);
	}

	public function delete(?int $id = null){
		$deleted = false;
		$model = model(DreamModel::class);
		$data = $model->find($id);
		if($data && $data['created_by'] == $this->session->user_id){
			$deleted = $model->remove($id);
		}
		if($deleted){
			session()->setFlashdata('success', 'Dream has been deleted.');
		}else{
			session()->setFlashdata('error', 'Dream could not be deleted.');
		}
		return redirect()->to('user/'. $this->session->user_id .'/dreams');
	}

	public function add($error = 0){
		helper('form');
		$data['title'] = 'Log a Dream';
		$data['error'] = $error;
		$data['visibility'] = $this->current_user['default_dream_visibility'] ?? 'private';
		$data['tags'] = '';
		$data['lexicon'] = DreamSymbols::toJson();
		$data['page_js'] = 'dream-form.js';
		return view('templates/header', $data)
			. view('dreams/add-edit', $data)
			. view('templates/footer', $data);
    }

	public function addSubmit(){
	    helper('form');
		$data = $this->request->getPost(['date', 'title','full_description','tags','visibility']);

		if (! $this->validateData($data, self::RULES)){
            return $this->add(1);
        }
		$tags = $this->parse_tags($data['tags']);
		if(count($tags) > DreamModel::MAX_TAGS){
			session()->setFlashdata('error', 'Cannot use more than 15 tags.');
            return $this->add(1);
		}

		$model = model(DreamModel::class);
		$model->save([
			'created_by' => $this->session->user_id,
			'date'	=> $data['date'],
			'title'	=> $data['title'], // Save raw, escape on output
			'full_description' => $data['full_description'],
			'visibility' => $data['visibility'],
        ]);
		$this->save_tags($model->insertID, $tags);

		session()->setFlashdata('success', 'Dream has been logged.');
		if(! empty($this->current_user['ai_analysis_enabled'])){
			// straight to Readings, which spins until this dream's reading is written
			return redirect()->to('user/'. $this->session->user_id .'/analysis');
		}
		return redirect()->to('user/'. $this->session->user_id .'/dreams');
	}

	/* "I've dreamt this too": toggles the viewer's mark on a dream they can see, returns JSON */
	public function resonate(int $id){
		$viewer_id = (int) $this->session->user_id;
		$linked = model(LinkModel::class)->linked_user_ids($viewer_id);
		$dream = model(DreamModel::class)->visible_to($viewer_id, $linked, null, false)
										 ->where('dreams.id', $id)
										 ->first();
		if(!$dream || (int) $dream['created_by'] === $viewer_id){
			return $this->response->setStatusCode(404)->setJSON(['error' => 'Dream not found.']);
		}
		$resonance = model(ResonanceModel::class);
		$on = $resonance->toggle($id, $viewer_id);
		return $this->response->setJSON(['on' => $on, 'count' => $resonance->count_for($id)]);
	}

	/** Tagify posts JSON like [{"value":"ocean"}]; without JavaScript it is plain "a,b". @return list<string> */
	private function parse_tags($json){
		$decoded = json_decode((string) $json);
		if(!is_array($decoded)){
			$decoded = array_map(fn ($v) => (object) ['value' => $v], explode(',', (string) $json));
		}
		$tags = [];
		foreach($decoded as $tag){
			$value = trim(mb_substr((string) ($tag->value ?? ''), 0, 25));
			if($value !== ''){
				$tags[] = $value;
			}
		}
		return array_values(array_unique($tags));
	}

	private function save_tags($dream_id, array $tags){
		model(TagModel::class)->replace_for_dream((int) $dream_id, $tags);
	}
}
