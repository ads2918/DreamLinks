<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * - dream_resonance: "I've dreamt this too" marks, one per dreamer per dream
 * - users: morning reminder settings
 * - dreams.ai_prompt_id was a tinyint, which overflows after prompt #127
 * - indexes for the tag and dream lookups the feed and dreamscape run
 */
class ResonanceRemindersIndexes extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id'               => ['type' => 'INT', 'auto_increment' => true],
			'dream_id'         => ['type' => 'INT'],
			'user_id'          => ['type' => 'INT'],
			'created_datetime' => ['type' => 'DATETIME', 'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP')],
		]);
		$this->forge->addPrimaryKey('id');
		$this->forge->addUniqueKey(['dream_id', 'user_id']);
		$this->forge->addKey('user_id');
		$this->forge->createTable('dream_resonance', true);

		$this->forge->addColumn('users', [
			'reminder_enabled'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
			'reminder_hour'      => ['type' => 'TINYINT', 'constraint' => 2, 'default' => 8],
			'timezone'           => ['type' => 'VARCHAR', 'constraint' => 64, 'default' => 'America/Indianapolis'],
			'reminder_last_sent' => ['type' => 'DATE', 'null' => true],
		]);

		$this->forge->modifyColumn('dreams', [
			'ai_prompt_id' => ['type' => 'INT', 'null' => false, 'default' => 0],
		]);

		$this->db->query('CREATE INDEX tags_dream_id ON tags (dream_id)');
		$this->db->query('CREATE INDEX tags_tag ON tags (tag)');
		$this->db->query('CREATE INDEX dreams_created_by ON dreams (created_by)');
	}

	public function down()
	{
		$this->db->query('DROP INDEX dreams_created_by ON dreams');
		$this->db->query('DROP INDEX tags_tag ON tags');
		$this->db->query('DROP INDEX tags_dream_id ON tags');
		$this->forge->modifyColumn('dreams', [
			'ai_prompt_id' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 0],
		]);
		$this->forge->dropColumn('users', ['reminder_enabled', 'reminder_hour', 'timezone', 'reminder_last_sent']);
		$this->forge->dropTable('dream_resonance', true);
	}
}
