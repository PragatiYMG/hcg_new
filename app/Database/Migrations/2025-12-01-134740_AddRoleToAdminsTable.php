<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleToAdminsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('admins', [
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['super_admin', 'admin', 'employee'],
                'default' => 'admin',
                'after' => 'email'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('admins', 'role');
    }
}
