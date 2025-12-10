<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'username'   => 'admin',
            'first_name' => 'Administrator',
            'last_name'  => '',
            'password'   => password_hash('admin123', PASSWORD_DEFAULT),
            'email'      => 'admin@hcg.com',
        ];

        $this->db->table('admins')->upsert($data);
    }
}
