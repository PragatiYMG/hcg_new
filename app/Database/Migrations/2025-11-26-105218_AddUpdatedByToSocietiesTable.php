<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedByToSocietiesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('societies', [
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
        $this->forge->dropColumn('societies', 'updated_by');
    }
}
