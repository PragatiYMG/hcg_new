<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVisibleToCustomerToAreasTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('areas', [
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
        $this->forge->dropColumn('areas', 'visible_to_customer');
    }
}
