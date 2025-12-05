<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSocietiesTable extends Migration
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
            'society_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
            ],
            'created_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->createTable('societies');
    }

    public function down()
    {
        $this->forge->dropTable('societies');
    }
}