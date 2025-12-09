<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissionsToAdd = [
            // Dashboard
            ['name' => 'dashboard.view', 'display_name' => 'View Dashboard', 'module' => 'Dashboard'],

            // Profile
            ['name' => 'profile.view', 'display_name' => 'View Profile', 'module' => 'Profile'],

            // Settings
            ['name' => 'settings.view', 'display_name' => 'View Settings', 'module' => 'Settings'],
            ['name' => 'settings.edit', 'display_name' => 'Edit Settings', 'module' => 'Settings'],

            // Logs
            ['name' => 'logs.view', 'display_name' => 'View Logs', 'module' => 'Logs'],

            // Bills
            ['name' => 'bills.view', 'display_name' => 'View Bills', 'module' => 'Bills'],
            ['name' => 'bills.create', 'display_name' => 'Create Bills', 'module' => 'Bills'],
            ['name' => 'bills.edit', 'display_name' => 'Edit Bills', 'module' => 'Bills'],
            ['name' => 'bills.delete', 'display_name' => 'Delete Bills', 'module' => 'Bills'],
            ['name' => 'bills.activate', 'display_name' => 'Activate Bills', 'module' => 'Bills'],
            ['name' => 'bills.duplicate', 'display_name' => 'Duplicate Bills', 'module' => 'Bills'],

            // Access Management
            ['name' => 'access.view', 'display_name' => 'View Access Management', 'module' => 'Access Management'],

            // Admin Users
            ['name' => 'admin_users.view', 'display_name' => 'View Admin Users', 'module' => 'Admin Users'],
            ['name' => 'admin_users.create', 'display_name' => 'Create Admin Users', 'module' => 'Admin Users'],
            ['name' => 'admin_users.edit', 'display_name' => 'Edit Admin Users', 'module' => 'Admin Users'],
            ['name' => 'admin_users.delete', 'display_name' => 'Delete Admin Users', 'module' => 'Admin Users'],

            // Users
            ['name' => 'users.view', 'display_name' => 'View Users', 'module' => 'Users'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'module' => 'Users'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'module' => 'Users'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'module' => 'Users'],

            // Rates
            ['name' => 'rates.view', 'display_name' => 'View Rates', 'module' => 'Rates'],
            ['name' => 'rates.create', 'display_name' => 'Create Rates', 'module' => 'Rates'],
            ['name' => 'rates.edit', 'display_name' => 'Edit Rates', 'module' => 'Rates'],
            ['name' => 'rates.delete', 'display_name' => 'Delete Rates', 'module' => 'Rates'],

            // Masters
            ['name' => 'masters.areas', 'display_name' => 'Manage Areas', 'module' => 'Masters'],
            ['name' => 'masters.countries', 'display_name' => 'Manage Countries', 'module' => 'Masters'],
            ['name' => 'masters.states', 'display_name' => 'Manage States', 'module' => 'Masters'],
            ['name' => 'masters.cities', 'display_name' => 'Manage Cities', 'module' => 'Masters'],
            ['name' => 'masters.societies', 'display_name' => 'Manage Societies', 'module' => 'Masters'],
            ['name' => 'masters.stove_types', 'display_name' => 'Manage Stove Types', 'module' => 'Masters'],
            ['name' => 'masters.burner_counts', 'display_name' => 'Manage Burner Counts', 'module' => 'Masters'],
            ['name' => 'masters.connection_fees', 'display_name' => 'Manage Connection Fees', 'module' => 'Masters'],
            ['name' => 'masters.connection_statuses', 'display_name' => 'Manage Connection Statuses', 'module' => 'Masters'],
            ['name' => 'masters.rates', 'display_name' => 'Manage Rates', 'module' => 'Masters'],
            ['name' => 'masters.charges', 'display_name' => 'Manage Charges', 'module' => 'Masters'],
            ['name' => 'masters.taxes', 'display_name' => 'Manage Taxes', 'module' => 'Masters'],
            ['name' => 'masters.banks', 'display_name' => 'Manage Banks', 'module' => 'Masters'],
            ['name' => 'masters.images', 'display_name' => 'Manage Images', 'module' => 'Masters'],
            ['name' => 'masters.meter_contractors', 'display_name' => 'Manage Meter Contractors', 'module' => 'Masters'],
            ['name' => 'masters.meter_manufacturers', 'display_name' => 'Manage Meter Manufacturers', 'module' => 'Masters'],

            // Reports
            ['name' => 'reports.employee', 'display_name' => 'Employee Reports', 'module' => 'Reports'],
        ];

        // Insert all permissions (ignore duplicates)
        foreach ($permissionsToAdd as $permission) {
            // Check if permission already exists
            $existing = $this->db->table('permissions')
                ->where('name', $permission['name'])
                ->get()
                ->getRowArray();

            if (!$existing) {
                $this->db->table('permissions')->insert($permission);
            }
        }

        echo "Permissions seeding completed.\n";
    }
}
