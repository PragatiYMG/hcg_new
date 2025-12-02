<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProfilePictureToAdminsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('admins', [
            'profile_picture' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'email'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('admins', 'profile_picture');
    }
}
