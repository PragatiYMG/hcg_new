<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddActiveStatusToAdminsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('admins', [
            'active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => false,
                'after'      => 'sms_2fa_enabled'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('admins', 'active');
    }
}
