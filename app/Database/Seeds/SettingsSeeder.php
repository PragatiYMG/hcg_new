<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'site_name'        => 'HCG News',
                'site_description' => 'Your trusted source for the latest news and updates.',
                'logo_path'        => 'writable/uploads/1762522180_34002c0979398c376816.png',
                'favicon_path'     => 'writable/uploads/1762522201_4d18c464f5d8d93f6032.png',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'site_name'        => 'Sample News Portal',
                'site_description' => 'Stay informed with breaking news and in-depth analysis.',
                'logo_path'        => 'writable/uploads/1762522180_a80f792fa21b1cd6ef9e.png',
                'favicon_path'     => 'writable/uploads/1762522201_64893967043f82519fd0.png',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
            [
                'site_name'        => 'Daily Updates',
                'site_description' => 'Daily news updates from around the world.',
                'logo_path'        => null,
                'favicon_path'     => null,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('settings')->insertBatch($data);
    }
}