<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFullRateToRatesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('rates', [
            'full_rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'null'       => false,
                'after'      => 'basic_rate',
            ],
        ]);

        // Modify basic_rate to DECIMAL(10,3) for more precision
        $this->forge->modifyColumn('rates', [
            'basic_rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
                'null'       => false,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('rates', 'full_rate');
    }
}
