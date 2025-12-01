<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedByToAreasTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('areas', [
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'created_by',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('areas', 'updated_by');
    }
}
