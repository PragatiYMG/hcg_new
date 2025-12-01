<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuthPermissionsUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'permission_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey(['user_id', 'permission_id'], true);
        $this->forge->createTable('auth_permissions_users');
    }

    public function down()
    {
        $this->forge->dropTable('auth_permissions_users');
    }
}