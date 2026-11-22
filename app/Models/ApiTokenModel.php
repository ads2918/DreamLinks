<?php

namespace App\Models;

use CodeIgniter\Model;

/** Mobile app sign-ins. The app keeps the token; the database only keeps its hash. */
class ApiTokenModel extends Model
{
	protected $table         = 'api_tokens';
	protected $allowedFields = ['user_id', 'token_hash', 'name', 'created_at', 'last_used_at'];

	/** @return string the new token, shown to the app once */
	public function issue(int $user_id, string $name): string
	{
		$token = bin2hex(random_bytes(32));
		$this->insert([
			'user_id'    => $user_id,
			'token_hash' => hash('sha256', $token),
			'name'       => mb_substr($name, 0, 100),
			'created_at' => date('Y-m-d H:i:s'),
		]);
		return $token;
	}

	/** @return array|null the approved, not deleted user who owns this token */
	public function user_for(string $token): ?array
	{
		if (! preg_match('/^[a-f0-9]{64}$/', $token)) {
			return null;
		}
		$row = $this->where('token_hash', hash('sha256', $token))->first();
		if (! $row) {
			return null;
		}
		$user = model(UserModel::class)->where('id', $row['user_id'])->where('deleted', 0)->where('status', 'approved')->first();
		if (! $user) {
			return null;
		}
		// one write a minute at most, so busy apps don't hammer the table
		if (! $row['last_used_at'] || strtotime($row['last_used_at']) < time() - 60) {
			$this->update($row['id'], ['last_used_at' => date('Y-m-d H:i:s')]);
		}
		$user['token_id'] = (int) $row['id'];
		return $user;
	}

	public function revoke_all(int $user_id): void
	{
		$this->where('user_id', $user_id)->delete();
	}
}
