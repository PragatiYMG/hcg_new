<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConvertChargesToDynamicStructure extends Migration
{
    public function up()
    {
        // Drop old fixed charge columns
        $this->forge->dropColumn('charges', 'late_charge');
        $this->forge->dropColumn('charges', 'average_charge');
        $this->forge->dropColumn('charges', 'bounce_charge');
        $this->forge->dropColumn('charges', 'no_of_days');
        $this->forge->dropColumn('charges', 'annual_charges');
        $this->forge->dropColumn('charges', 'minimum_charges');

        // Add new dynamic columns
        $this->forge->addColumn('charges', [
            'charge_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
                'after'      => 'id',
            ],
            'charge_value' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'null'       => false,
                'after'      => 'charge_name',
            ],
        ]);
    }

    public function down()
    {
        // Drop new columns
        $this->forge->dropColumn('charges', 'charge_name');
        $this->forge->dropColumn('charges', 'charge_value');

        // Add back old columns
        $this->forge->addColumn('charges', [
            'late_charge' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'null'       => false,
            ],
            'average_charge' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'null'       => false,
            ],
            'bounce_charge' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'null'       => false,
            ],
            'no_of_days' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'null'       => false,
            ],
            'annual_charges' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'null'       => false,
            ],
            'minimum_charges' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
                'null'       => false,
            ],
        ]);
    }
}
