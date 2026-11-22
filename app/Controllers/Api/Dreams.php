<?php

namespace App\Controllers\Api;

use App\Libraries\DreamFeed;
use App\Models\DreamModel;
use App\Models\LinkModel;
use App\Models\ResonanceModel;
use App\Models\TagModel;

class Dreams extends ApiController
{
	/** GET api/v1/feed?page= : dreams by others the user can see, newest first, plus the 30-day pulse */
	public function feed()
	{
		[$limit, $offset] = $this->paging();
		$linked = model(LinkModel::class)->linked_user_ids($this->uid());
		$rows = model(DreamModel::class)->visible_to($this->uid(), $linked, null, false)->findAll($limit, $offset);
		$out = ['dreams' => array_map([$this, 'dream'], DreamFeed::decorate($rows, $this->uid()))];
		if ($offset === 0) {
			$out['pulse'] = DreamFeed::pulse($this->uid(), $linked);
		}
		return $this->respond($out);
	}

	/** GET api/v1/dreams?page= : the user's own journal, private dreams included */
	public function index()
	{
		[$limit, $offset] = $this->paging();
		$rows = model(DreamModel::class)->visible_to($this->uid(), [], [$this->uid()])->findAll($limit, $offset);
		return $this->respond(['dreams' => array_map([$this, 'dream'], DreamFeed::decorate($rows, $this->uid()))]);
	}

	/** GET api/v1/dreams/{id} : any dream the user is allowed to see */
	public function show(int $id)
	{
		$row = $this->visible($id);
		return $row ? $this->respond($this->dream($row)) : $this->failNotFound('Dream not found.');
	}

	/** POST api/v1/dreams {title, text, date, visibility, tags[]} */
	public function create()
	{
		$fields = $this->fields();
		if (isset($fields['errors'])) {
			return $this->invalid($fields['errors']);
		}
		$model = model(DreamModel::class);
		$model->insert($fields['dream'] + ['created_by' => $this->uid()]);
		$id = (int) $model->getInsertID();
		model(TagModel::class)->replace_for_dream($id, $fields['tags']);
		return $this->respondCreated($this->dream($this->visible($id)));
	}

	/** PUT api/v1/dreams/{id} : same fields as create, own dreams only */
	public function update(int $id)
	{
		if (! $this->owned($id)) {
			return $this->failNotFound('Dream not found.');
		}
		$fields = $this->fields();
		if (isset($fields['errors'])) {
			return $this->invalid($fields['errors']);
		}
		model(DreamModel::class)->update($id, $fields['dream']);
		model(TagModel::class)->replace_for_dream($id, $fields['tags']);
		return $this->respond($this->dream($this->visible($id)));
	}

	/** DELETE api/v1/dreams/{id} */
	public function delete(int $id)
	{
		if (! $this->owned($id) || ! model(DreamModel::class)->remove($id)) {
			return $this->failNotFound('Dream not found.');
		}
		return $this->respondDeleted(['ok' => true]);
	}

	/** POST api/v1/dreams/{id}/resonate : toggles "I've dreamt this too" on someone else's dream */
	public function resonate(int $id)
	{
		$row = $this->visible($id);
		if (! $row || (int) $row['created_by'] === $this->uid()) {
			return $this->failNotFound('Dream not found.');
		}
		$resonance = model(ResonanceModel::class);
		$on = $resonance->toggle($id, $this->uid());
		return $this->respond(['on' => $on, 'count' => $resonance->count_for($id)]);
	}

	/** One dream if the user may see it, decorated, or null. */
	private function visible(int $id): ?array
	{
		$linked = model(LinkModel::class)->linked_user_ids($this->uid());
		$row = model(DreamModel::class)->visible_to($this->uid(), $linked)->where('dreams.id', $id)->first();
		return $row ? DreamFeed::decorate([$row], $this->uid())[0] : null;
	}

	private function owned(int $id): bool
	{
		$row = model(DreamModel::class)->find($id);
		return $row && (int) $row['created_by'] === $this->uid();
	}

	/** Validated dream fields and tags from the request, or ['errors' => [...]]. */
	private function fields(): array
	{
		$in = $this->input();
		$data = [
			'title'            => trim((string) ($in['title'] ?? '')),
			'full_description' => trim((string) ($in['text'] ?? '')),
			'date'             => trim((string) ($in['date'] ?? '')),
			'visibility'       => (string) ($in['visibility'] ?? ''),
		];
		$validation = service('validation')->setRules(DreamModel::RULES);
		if (! $validation->run($data)) {
			$errors = $validation->getErrors();
			if (isset($errors['full_description'])) {
				$errors['text'] = $errors['full_description'];
				unset($errors['full_description']);
			}
			return ['errors' => $errors];
		}
		$raw = $in['tags'] ?? [];
		$tags = TagModel::clean(is_array($raw) ? $raw : explode(',', (string) $raw));
		if (count($tags) > DreamModel::MAX_TAGS) {
			return ['errors' => ['tags' => 'Use ' . DreamModel::MAX_TAGS . ' tags or fewer.']];
		}
		return ['dream' => $data, 'tags' => $tags];
	}
}
