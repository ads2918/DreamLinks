<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Open sign-up: new accounts start 'unconfirmed' until the emailed link is opened (see App\Libraries\Signup). */
class UnconfirmedStatus extends Migration
{
	public function up()
	{
		$this->db->query("ALTER TABLE users MODIFY status ENUM('pending_approval','approved','unconfirmed','') NOT NULL DEFAULT 'unconfirmed'");
	}

	public function down()
	{
		$this->db->query("UPDATE users SET status = 'pending_approval' WHERE status = 'unconfirmed'");
		$this->db->query("ALTER TABLE users MODIFY status ENUM('pending_approval','approved','') NOT NULL DEFAULT 'pending_approval'");
	}
}
