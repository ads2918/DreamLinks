<?php

namespace App\Controllers\Api;

use App\Libraries\DreamFeed;
use App\Models\BlockModel;
use App\Models\DreamModel;
use App\Models\LinkModel;
use App\Models\ReportModel;
use App\Models\UserModel;

/** Other dreamers: profiles, search and links. Blocks hide people from each other both ways. */
class People extends ApiController
{
	/** GET api/v1/users/{id} : profile, link state and the dreams the user may see */
	public function show(int $id)
	{
		$person = $this->findPerson($id);
		if (! $person) {
			return $this->failNotFound('Dreamer not found.');
		}
		$linked = model(LinkModel::class)->linked_user_ids($this->uid());
		$rows = model(DreamModel::class)->visible_to($this->uid(), $linked, [$id])->findAll(50);
		if ($id === $this->uid()) {
			// your own profile shows what others see, not your private dreams
			$rows = array_values(array_filter($rows, fn ($d) => $d['visibility'] !== 'private'));
		}
		return $this->respond([
			'user'   => $this->person($person) + ['bio' => $person['bio'], 'self' => $id === $this->uid(), 'link' => $this->linkState($id)],
			'dreams' => array_map([$this, 'dream'], DreamFeed::decorate($rows, $this->uid())),
		]);
	}

	/** GET api/v1/search?q= */
	public function search()
	{
		$q = trim((string) $this->request->getGet('q'));
		if (mb_strlen($q) < 2) {
			return $this->respond(['users' => []]);
		}
		$rows = model(UserModel::class)->like('username', $q)->where('deleted', 0)->where('status', 'approved')
			->where('id !=', $this->uid())->orderBy('username')->findAll(30);
		$users = [];
		foreach ($rows as $row) {
			if (! $this->blockedEitherWay((int) $row['id'])) {
				$users[] = $this->person($row) + ['link' => $this->linkState((int) $row['id'])];
			}
		}
		return $this->respond(['users' => $users]);
	}

	/** GET api/v1/links : linked dreamers and requests waiting on the user */
	public function links()
	{
		$model = model(LinkModel::class);
		$linked = [];
		foreach ($model->user_approved_links($this->uid()) as $l) {
			$linked[] = ['link_id' => (int) $l['id'], 'id' => (int) $l['profile_id'], 'username' => $l['username'], 'avatar' => self::avatar($l['image'])];
		}
		$pending = [];
		foreach ($model->user_pending_links($this->uid()) as $l) {
			$pending[] = ['link_id' => (int) $l['id'], 'id' => (int) $l['request_user_id'], 'username' => $l['username'], 'avatar' => self::avatar($l['image'])];
		}
		return $this->respond(['linked' => $linked, 'pending' => $pending]);
	}

	/** POST api/v1/users/{id}/link : ask to link */
	public function requestLink(int $id)
	{
		if ($id === $this->uid() || ! $this->findPerson($id)) {
			return $this->failNotFound('Dreamer not found.');
		}
		$model = model(LinkModel::class);
		if (! $model->link_request_pending($this->uid(), $id)) {
			$model->insert(['request_user_id' => $this->uid(), 'recipient_user_id' => $id, 'request_datetime' => date('Y-m-d H:i:s')]);
		}
		return $this->respond(['link' => $this->linkState($id)]);
	}

	/** POST api/v1/links/{id}/accept and /decline : only the person who was asked */
	public function answer(int $link_id, string $answer)
	{
		$model = model(LinkModel::class);
		$link = $model->find($link_id);
		if (! $link || (int) $link['recipient_user_id'] !== $this->uid() || $link['request_approved'] || $link['decline_flag']) {
			return $this->failNotFound('Request not found.');
		}
		$model->update($link_id, $answer === 'accept'
			? ['request_approved' => 1, 'request_approved_datetime' => date('Y-m-d H:i:s')]
			: ['decline_flag' => 1, 'decline_datetime' => date('Y-m-d H:i:s')]);
		return $this->links();
	}

	/** DELETE api/v1/links/{id} : either side can unlink */
	public function unlink(int $link_id)
	{
		$model = model(LinkModel::class);
		$link = $model->find($link_id);
		if (! $link || ! in_array($this->uid(), [(int) $link['request_user_id'], (int) $link['recipient_user_id']], true)) {
			return $this->failNotFound('Link not found.');
		}
		$model->decline_link_by_id($link_id);
		return $this->links();
	}

	/** POST api/v1/users/{id}/block : hides the two of you from each other and ends any link, like the website */
	public function block(int $id)
	{
		if ($id === $this->uid() || ! model(UserModel::class)->where('id', $id)->where('deleted', 0)->first()) {
			return $this->failNotFound('Dreamer not found.');
		}
		$model = model(BlockModel::class);
		if (! $model->block_exsists($id, $this->uid())) {
			$model->insert(['blocked_user_id' => $id, 'blocked_by_user_id' => $this->uid(), 'blocked_datetime' => date('Y-m-d H:i:s')]);
		}
		model(LinkModel::class)->decline_links($id, $this->uid());
		return $this->blocks();
	}

	/** GET api/v1/blocks : dreamers this user has blocked */
	public function blocks()
	{
		$blocked = [];
		foreach (model(BlockModel::class)->block_list($this->uid()) as $b) {
			$blocked[] = ['block_id' => (int) $b['id'], 'id' => (int) $b['blocked_user_id'], 'username' => $b['username'], 'avatar' => self::avatar($b['image'])];
		}
		return $this->respond(['blocked' => $blocked]);
	}

	/** DELETE api/v1/blocks/{id} : only the one who blocked can unblock */
	public function unblock(int $block_id)
	{
		$model = model(BlockModel::class);
		$block = $model->find($block_id);
		if (! $block || (int) $block['blocked_by_user_id'] !== $this->uid()) {
			return $this->failNotFound('Block not found.');
		}
		$model->unblock($block_id, $this->uid());
		return $this->blocks();
	}

	/** POST api/v1/reports {dream_id or user_id, reason, note} : see ReportModel::REASONS */
	public function report()
	{
		$in = $this->input();
		$error = ReportModel::file($this->uid(), (int) ($in['user_id'] ?? 0) ?: null, (int) ($in['dream_id'] ?? 0) ?: null,
			(string) ($in['reason'] ?? ''), (string) ($in['note'] ?? ''));
		return $error ? $this->invalid(['reason' => $error]) : $this->respond(['ok' => true]);
	}

	private function findPerson(int $id): ?array
	{
		$person = model(UserModel::class)->where('id', $id)->where('deleted', 0)->first();
		if (! $person || ($id !== $this->uid() && $this->blockedEitherWay($id))) {
			return null;
		}
		return $person;
	}

	private function blockedEitherWay(int $id): bool
	{
		return (bool) model(BlockModel::class)->block_exsists($id, $this->uid());
	}

	private function person(array $u): array
	{
		return ['id' => (int) $u['id'], 'username' => $u['username'], 'avatar' => self::avatar($u['image'])];
	}

	/** none | requested (you asked) | incoming (they asked) | linked, plus the link id when there is one */
	private function linkState(int $id): array
	{
		$rows = model(LinkModel::class)->link_request_pending($this->uid(), $id);
		if (! $rows) {
			return ['state' => 'none', 'link_id' => null];
		}
		$l = $rows[0];
		$state = $l['request_approved'] ? 'linked' : ((int) $l['request_user_id'] === $this->uid() ? 'requested' : 'incoming');
		return ['state' => $state, 'link_id' => (int) $l['id']];
	}
}
