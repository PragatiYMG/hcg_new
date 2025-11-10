<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'key' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'value' => [
                'type' => 'TEXT',
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
        $this->forge->addUniqueKey('key');
        $this->forge->createTable('settings');

        // Insert default settings
        $this->db->table('settings')->insertBatch([
            ['key' => 'site_name', 'value' => '', 'created_at' => date('Y-m-d H:i:s')],
            ['key' => 'site_description', 'value' => '', 'created_at' => date('Y-m-d H:i:s')],
            ['key' => 'site_logo', 'value' => '', 'created_at' => date('Y-m-d H:i:s')],
            ['key' => 'site_favicon', 'value' => '', 'created_at' => date('Y-m-d H:i:s')]
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('settings');
    }
}
