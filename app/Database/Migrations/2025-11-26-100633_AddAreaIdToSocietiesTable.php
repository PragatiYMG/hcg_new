<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAreaIdToSocietiesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('societies', [
            'area_id' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => false,
                'after'      => 'id',
            ],
        ]);

        // Add foreign key constraint
        $this->forge->addForeignKey('area_id', 'areas', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->forge->dropForeignKey('societies', 'societies_area_id_foreign');
        $this->forge->dropColumn('societies', 'area_id');
    }
}
