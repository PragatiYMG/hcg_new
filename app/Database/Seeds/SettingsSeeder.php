<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'site_name'        => 'HCG',
                'site_description' => 'Your trusted source for the latest news and updates.',
                'logo_path'        => '1762522180_34002c0979398c376816.png',
                'favicon_path'     => '1762522201_4d18c464f5d8d93f6032.png',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('settings')->insertBatch($data);
    }
}