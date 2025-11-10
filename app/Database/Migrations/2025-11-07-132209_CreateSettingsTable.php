<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingsTable extends Migration
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
            'site_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'site_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'logo_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'favicon_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('settings');
    }

    public function down()
    {
        $this->forge->dropTable('settings');
    }
}
