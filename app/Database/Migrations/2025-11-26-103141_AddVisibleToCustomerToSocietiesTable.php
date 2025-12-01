<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVisibleToCustomerToSocietiesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('societies', [
            'visible_to_customer' => [
                'type'       => 'ENUM',
                'constraint' => ['yes', 'no'],
                'default'    => 'yes',
                'after'      => 'status',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('societies', 'visible_to_customer');
    }
}
