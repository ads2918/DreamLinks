<?php

namespace App\Models;

use CodeIgniter\Model;

/* "I've dreamt this too" marks: one row per dreamer per dream */
class ResonanceModel extends Model
{
	protected $table         = 'dream_resonance';
	protected $allowedFields = ['dream_id', 'user_id'];

	/** Adds the mark if missing, removes it if present. @return bool whether it is now on */
	public function toggle(int $dream_id, int $user_id): bool
	{
		$existing = $this->where('dream_id', $dream_id)->where('user_id', $user_id)->first();
		if ($existing) {
			$this->delete($existing['id']);
			return false;
		}
		$this->insert(['dream_id' => $dream_id, 'user_id' => $user_id]);
		return true;
	}

	public function count_for(int $dream_id): int
	{
		return $this->where('dream_id', $dream_id)->countAllResults();
	}

	/** @return array<int, int> mark counts keyed by dream id */
	public function counts_for(array $dream_ids): array
	{
		$counts = [];
		if (empty($dream_ids)) {
			return $counts;
		}
		$rows = $this->select('dream_id, COUNT(*) AS n')
					 ->whereIn('dream_id', array_map('intval', $dream_ids))
					 ->groupBy('dream_id')
					 ->findAll();
		foreach ($rows as $row) {
			$counts[(int) $row['dream_id']] = (int) $row['n'];
		}
		return $counts;
	}

	/** @return list<int> which of these dreams $user_id has marked */
	public function marked_by(int $user_id, array $dream_ids): array
	{
		if (empty($dream_ids)) {
			return [];
		}
		$rows = $this->select('dream_id')
					 ->where('user_id', $user_id)
					 ->whereIn('dream_id', array_map('intval', $dream_ids))
					 ->findAll();
		return array_map('intval', array_column($rows, 'dream_id'));
	}
}
