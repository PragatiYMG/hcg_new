<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolePermissionsSeeder extends Seeder
{
    public function run()
    {
        // Clear existing role permissions - no role-based permissions needed
        $this->db->table('role_permissions')->truncate();

        echo "Role permissions cleared. All permissions now managed by user permissions only.\n";
    }
}
