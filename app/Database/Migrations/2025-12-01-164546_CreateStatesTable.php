<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStatesTable extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
            ],
            'country_id' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
            ],
            'created_at' => [
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
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('country_id');
        $this->forge->addForeignKey('country_id', 'countries', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('states');
    }

    public function down()
    {
        $this->forge->dropTable('states');
    }
}