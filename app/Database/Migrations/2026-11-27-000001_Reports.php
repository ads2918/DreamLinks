<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Reports of dreams or dreamers that break the rules. Each one is also emailed to the admin. */
class Reports extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
			'reporter_id' => ['type' => 'INT'],
			'user_id'     => ['type' => 'INT'],
			'dream_id'    => ['type' => 'INT', 'null' => true],
			'reason'      => ['type' => 'VARCHAR', 'constraint' => 20],
			'note'        => ['type' => 'VARCHAR', 'constraint' => 500, 'default' => ''],
			'created_at'  => ['type' => 'DATETIME', 'null' => true],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addKey('user_id');
		$this->forge->addKey('reporter_id');
		$this->forge->createTable('reports', true);
	}

	public function down()
	{
		$this->forge->dropTable('reports', true);
	}
}
