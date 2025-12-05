<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuthRememberTokensTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'selector' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'hashedValidator' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'expires' => [
                'type' => 'DATETIME',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('selector', true);
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('auth_remember_tokens');
    }

    public function down()
    {
        $this->forge->dropTable('auth_remember_tokens');
    }
}