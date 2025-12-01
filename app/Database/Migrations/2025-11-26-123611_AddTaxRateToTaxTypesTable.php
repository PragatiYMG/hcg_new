<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTaxRateToTaxTypesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tax_types', [
            'tax_rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => '0.00',
                'null'       => false,
                'after'      => 'type_name',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tax_types', 'tax_rate');
    }
}
