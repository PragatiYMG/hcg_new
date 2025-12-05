<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMeterContractorsTable extends Migration
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
                'constraint' => '255',
            ],
            'company_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'contact_person' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'unique'     => true,
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'license_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
                'null'       => true,
            ],
            'specialization' => [
                'type'       => 'ENUM',
                'constraint' => ['electricity', 'water', 'gas', 'all'],
                'default'    => 'all',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
            ],
            'society_id' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->addForeignKey('society_id', 'societies', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('created_by', 'admins', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('meter_contractors');
    }

    public function down()
    {
        $this->forge->dropTable('meter_contractors');
    }
}