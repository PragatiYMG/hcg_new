<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateConnectionStatusesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'status_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'status_order' => [
                'type'       => 'INT',
                'constraint' => 3,
                'unsigned'   => true,
                'unique'     => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('connection_statuses');
    }

    public function down()
    {
        $this->forge->dropTable('connection_statuses');
    }
}
