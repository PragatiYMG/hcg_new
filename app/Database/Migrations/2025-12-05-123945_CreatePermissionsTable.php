<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePermissionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'display_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'module' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
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
        $this->forge->createTable('permissions');

        // Insert default permissions
        $permissions = [
            // User Management
            ['name' => 'users.view', 'display_name' => 'View Users', 'description' => 'Can view user list', 'module' => 'users'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'description' => 'Can create new users', 'module' => 'users'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'description' => 'Can edit existing users', 'module' => 'users'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'description' => 'Can delete users', 'module' => 'users'],

            // Masters
            ['name' => 'masters.view', 'display_name' => 'View Masters', 'description' => 'Can view master data', 'module' => 'masters'],
            ['name' => 'masters.areas', 'display_name' => 'Manage Areas', 'description' => 'Can manage areas', 'module' => 'masters'],
            ['name' => 'masters.societies', 'display_name' => 'Manage Societies', 'description' => 'Can manage societies', 'module' => 'masters'],
            ['name' => 'masters.connection_statuses', 'display_name' => 'Manage Connection Statuses', 'description' => 'Can manage connection statuses', 'module' => 'masters'],
            ['name' => 'masters.meter_contractors', 'display_name' => 'Manage Meter Contractors', 'description' => 'Can manage meter contractors', 'module' => 'masters'],
            ['name' => 'masters.meter_manufacturers', 'display_name' => 'Manage Meter Manufacturers', 'description' => 'Can manage meter manufacturers', 'module' => 'masters'],
            ['name' => 'masters.stove_types', 'display_name' => 'Manage Stove Types', 'description' => 'Can manage stove types', 'module' => 'masters'],
            ['name' => 'masters.burner_counts', 'display_name' => 'Manage Burner Counts', 'description' => 'Can manage burner counts', 'module' => 'masters'],
            ['name' => 'masters.connection_fees', 'display_name' => 'Manage Connection Fees', 'description' => 'Can manage connection fees', 'module' => 'masters'],
            ['name' => 'masters.rates', 'display_name' => 'Manage Rates', 'description' => 'Can manage rates', 'module' => 'masters'],
            ['name' => 'masters.charges', 'display_name' => 'Manage Charges', 'description' => 'Can manage charges', 'module' => 'masters'],
            ['name' => 'masters.taxes', 'display_name' => 'Manage Taxes', 'description' => 'Can manage taxes', 'module' => 'masters'],
            ['name' => 'masters.banks', 'display_name' => 'Manage Banks', 'description' => 'Can manage banks', 'module' => 'masters'],
            ['name' => 'masters.images', 'display_name' => 'Manage Images', 'description' => 'Can manage images', 'module' => 'masters'],

            // Bills Management
            ['name' => 'bills.view', 'display_name' => 'View Bills', 'description' => 'Can view bills', 'module' => 'bills'],
            ['name' => 'bills.create', 'display_name' => 'Create Bills', 'description' => 'Can create new bills', 'module' => 'bills'],
            ['name' => 'bills.edit', 'display_name' => 'Edit Bills', 'description' => 'Can edit existing bills', 'module' => 'bills'],
            ['name' => 'bills.delete', 'display_name' => 'Delete Bills', 'description' => 'Can delete bills', 'module' => 'bills'],

            // Settings (Super Admin Only)
            ['name' => 'settings.view', 'display_name' => 'View Settings', 'description' => 'Can view system settings', 'module' => 'settings'],
            ['name' => 'settings.edit', 'display_name' => 'Edit Settings', 'description' => 'Can edit system settings', 'module' => 'settings'],

            // Access Management (Super Admin Only)
            ['name' => 'access.view', 'display_name' => 'View Access Management', 'description' => 'Can view access management', 'module' => 'access'],
            ['name' => 'access.edit', 'display_name' => 'Edit Access Management', 'description' => 'Can edit user and role permissions', 'module' => 'access'],

            // Profile Management
            ['name' => 'profile.view', 'display_name' => 'View Profile', 'description' => 'Can view own profile', 'module' => 'profile'],
            ['name' => 'profile.edit', 'display_name' => 'Edit Profile', 'description' => 'Can edit own profile', 'module' => 'profile'],

            // Dashboard
            ['name' => 'dashboard.view', 'display_name' => 'View Dashboard', 'description' => 'Can access dashboard', 'module' => 'dashboard'],

            // Reports
            ['name' => 'reports.view', 'display_name' => 'View Reports', 'description' => 'Can view reports', 'module' => 'reports'],
            ['name' => 'reports.employee', 'display_name' => 'Employee Reports', 'description' => 'Can view employee reports', 'module' => 'reports'],

            // Logs
            ['name' => 'logs.view', 'display_name' => 'View Logs', 'description' => 'Can view system logs', 'module' => 'logs'],
        ];

        $this->db->table('permissions')->insertBatch($permissions);
    }

    public function down()
    {
        $this->forge->dropTable('permissions');
    }
}
