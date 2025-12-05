<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class AddUpdatedAtToAreasTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('areas', [
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => new RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('areas', 'updated_at');
    }
}