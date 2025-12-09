<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSecurityFieldsToAdminsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('admins', [
            'mobile' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
                'unique'     => true,
                'after'      => 'email'
            ],
            'force_password_reset' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'role'
            ],
            'sms_2fa_enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'force_password_reset'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('admins', ['mobile', 'force_password_reset', 'sms_2fa_enabled']);
    }
}
