<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Sign-in tokens for the mobile app. Only a SHA-256 hash of each token is stored. */
class ApiTokens extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
			'user_id'      => ['type' => 'INT'],
			'token_hash'   => ['type' => 'CHAR', 'constraint' => 64],
			'name'         => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => ''],
			'created_at'   => ['type' => 'DATETIME', 'null' => true],
			'last_used_at' => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addUniqueKey('token_hash');
		$this->forge->addKey('user_id');
		$this->forge->createTable('api_tokens', true);
	}

	public function down()
	{
		$this->forge->dropTable('api_tokens', true);
	}
}
