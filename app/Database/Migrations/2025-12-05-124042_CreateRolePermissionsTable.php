<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRolePermissionsTable extends Migration
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
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'permission_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('permission_id', 'permissions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('role_permissions');

        // Insert default role permissions
        // Super Admin - All permissions
        $superAdminPermissions = [
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'masters.view', 'masters.areas', 'masters.societies', 'masters.connection_statuses',
            'masters.meter_contractors', 'masters.meter_manufacturers', 'masters.stove_types',
            'masters.burner_counts', 'masters.connection_fees', 'masters.rates', 'masters.charges',
            'masters.taxes', 'masters.banks', 'masters.images',
            'bills.view', 'bills.create', 'bills.edit', 'bills.delete',
            'settings.view', 'settings.edit',
            'access.view', 'access.edit',
            'profile.view', 'profile.edit',
            'dashboard.view',
            'reports.view', 'reports.employee',
            'logs.view'
        ];

        // Admin - Most permissions except settings and access management
        $adminPermissions = [
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'masters.view', 'masters.areas', 'masters.societies', 'masters.connection_statuses',
            'masters.meter_contractors', 'masters.meter_manufacturers', 'masters.stove_types',
            'masters.burner_counts', 'masters.connection_fees', 'masters.rates', 'masters.charges',
            'masters.taxes', 'masters.banks', 'masters.images',
            'bills.view', 'bills.create', 'bills.edit', 'bills.delete',
            'profile.view', 'profile.edit',
            'dashboard.view',
            'reports.view', 'reports.employee'
        ];

        // Employee - Limited permissions (will be assigned individually)
        $employeePermissions = [
            'profile.view', 'profile.edit',
            'dashboard.view'
        ];

        $rolePermissions = [];

        // Get permission IDs
        $permissions = $this->db->table('permissions')->get()->getResultArray();
        $permissionMap = [];
        foreach ($permissions as $perm) {
            $permissionMap[$perm['name']] = $perm['id'];
        }

        // Super Admin permissions
        foreach ($superAdminPermissions as $perm) {
            if (isset($permissionMap[$perm])) {
                $rolePermissions[] = [
                    'role' => 'super_admin',
                    'permission_id' => $permissionMap[$perm]
                ];
            }
        }

        // Admin permissions
        foreach ($adminPermissions as $perm) {
            if (isset($permissionMap[$perm])) {
                $rolePermissions[] = [
                    'role' => 'admin',
                    'permission_id' => $permissionMap[$perm]
                ];
            }
        }

        // Employee permissions
        foreach ($employeePermissions as $perm) {
            if (isset($permissionMap[$perm])) {
                $rolePermissions[] = [
                    'role' => 'employee',
                    'permission_id' => $permissionMap[$perm]
                ];
            }
        }

        if (!empty($rolePermissions)) {
            $this->db->table('role_permissions')->insertBatch($rolePermissions);
        }
    }

    public function down()
    {
        $this->forge->dropTable('role_permissions');
    }
}
