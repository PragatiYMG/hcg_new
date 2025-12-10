<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedByToDepartmentsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('departments', [
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'created_by'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('departments', 'updated_by');
    }
}