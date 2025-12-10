<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyAdminsTableForNameAndDepartment extends Migration
{
    public function up()
    {
        // Add new columns
        $this->forge->addColumn('admins', [
            'first_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'email'
            ],
            'last_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'first_name'
            ],
            'department_id' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'last_name'
            ]
        ]);

        // Add foreign key constraint
        $this->db->query('ALTER TABLE admins ADD CONSTRAINT fk_admins_department_id FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL');

        // Drop old name column
        $this->forge->dropColumn('admins', 'name');
    }

    public function down()
    {
        // Add back name column
        $this->forge->addColumn('admins', [
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'email'
            ]
        ]);

        // Drop foreign key and new columns
        $this->db->query('ALTER TABLE admins DROP FOREIGN KEY fk_admins_department_id');
        $this->forge->dropColumn('admins', 'department_id');
        $this->forge->dropColumn('admins', 'last_name');
        $this->forge->dropColumn('admins', 'first_name');
    }
}