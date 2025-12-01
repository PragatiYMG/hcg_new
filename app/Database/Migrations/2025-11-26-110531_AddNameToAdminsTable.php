<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNameToAdminsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('admins', [
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'username',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('admins', 'name');
    }
}
