<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropOldBillColumns extends Migration
{
    public function up()
    {
        // Drop old columns that are now duplicated by new specific fields
        $columnsToDrop = [
            'name',           // Replaced by company_name
            'email',          // Replaced by customer_care_email
            'address',        // Replaced by registered_office_address
            'phone',          // Replaced by customer_care_phones
            'emergency_no',   // Replaced by emergency_contact
            'website',        // Replaced by website_link
            'active'          // Replaced by status (active/inactive/draft)
        ];

        foreach ($columnsToDrop as $column) {
            $this->forge->dropColumn('bills', $column);
        }
    }

    public function down()
    {
        // Add back the old columns if needed for rollback
        $this->forge->addColumn('bills', [
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'after'      => 'id',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '191',
                'after'      => 'tag_line',
            ],
            'address' => [
                'type' => 'TEXT',
                'after' => 'email',
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'after'      => 'address',
            ],
            'emergency_no' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'after'      => 'phone',
            ],
            'website' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'after'      => 'emergency_no',
            ],
            'active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'footer_description',
            ],
        ]);
    }
}
