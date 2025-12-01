<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuthLoginsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'user_agent' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'id_type' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'identifier' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'null' => true,
            ],
            'date' => [
                'type' => 'DATETIME',
            ],
            'success' => [
                'type' => 'TINYINT',
                'constraint' => 1,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['success', 'user_id', 'date']);
        $this->forge->createTable('auth_logins');
    }

    public function down()
    {
        $this->forge->dropTable('auth_logins');
    }
}