<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AreaSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'area_name'   => 'Downtown',
                'status'      => 'active',
                'created_date' => date('Y-m-d H:i:s'),
                'created_by'  => 1,
            ],
            [
                'area_name'   => 'Uptown',
                'status'      => 'active',
                'created_date' => date('Y-m-d H:i:s'),
                'created_by'  => 1,
            ],
            [
                'area_name'   => 'Suburb',
                'status'      => 'inactive',
                'created_date' => date('Y-m-d H:i:s'),
                'created_by'  => 1,
            ],
        ];

        $this->db->table('areas')->insertBatch($data);
    }
}
