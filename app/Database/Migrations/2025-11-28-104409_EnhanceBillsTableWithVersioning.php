<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceBillsTableWithVersioning extends Migration
{
    public function up()
    {
        // Add versioning and new fields to bills table
        $this->forge->addColumn('bills', [
            // Versioning fields
            'version' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => '1.0',
                'after'      => 'id',
            ],
            'effective_date' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'version',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'active', 'inactive'],
                'default'    => 'draft',
                'after'      => 'effective_date',
            ],

            // Company Information
            'company_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'after'      => 'name',
            ],
            'company_name_short' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'company_name',
            ],

            // Addresses
            'registered_office_address' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'address',
            ],
            'corporate_office_address' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'registered_office_address',
            ],

            // Registration Numbers
            'cin_no' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'corporate_office_address',
            ],
            'gst_no' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'cin_no',
            ],

            // Contact Information
            'customer_care_email' => [
                'type'       => 'VARCHAR',
                'constraint' => '191',
                'after'      => 'email',
            ],
            'website_link' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'after'      => 'website',
            ],
            'emergency_contact' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'after'      => 'emergency_no',
            ],
            'customer_care_phones' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'phone',
            ],

            // Image References (foreign keys to images table)
            'logo_image_id' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'customer_care_phones',
            ],
            'signature_image_id' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'logo_image_id',
            ],

            // Content Fields
            'invoice_text' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'summary_description',
            ],
            'invoice_image_id' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'invoice_text',
            ],
        ]);

        // Add foreign key constraints for image references
        $this->forge->addForeignKey('logo_image_id', 'images', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('signature_image_id', 'images', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('invoice_image_id', 'images', 'id', 'SET NULL', 'CASCADE');

        // Update existing records to have default values
        $this->db->query("UPDATE bills SET version = '1.0', status = 'active' WHERE version IS NULL");
        $this->db->query("UPDATE bills SET company_name = name WHERE company_name IS NULL OR company_name = ''");
        $this->db->query("UPDATE bills SET customer_care_email = email WHERE customer_care_email IS NULL OR customer_care_email = ''");
        $this->db->query("UPDATE bills SET website_link = website WHERE website_link IS NULL OR website_link = ''");
        $this->db->query("UPDATE bills SET emergency_contact = emergency_no WHERE emergency_contact IS NULL OR emergency_contact = ''");
        $this->db->query("UPDATE bills SET registered_office_address = address WHERE registered_office_address IS NULL OR registered_office_address = ''");
    }

    public function down()
    {
        // Remove foreign key constraints
        $this->forge->dropForeignKey('bills', 'bills_logo_image_id_foreign');
        $this->forge->dropForeignKey('bills', 'bills_signature_image_id_foreign');
        $this->forge->dropForeignKey('bills', 'bills_invoice_image_id_foreign');

        // Remove added columns
        $columns = [
            'version',
            'effective_date',
            'status',
            'company_name',
            'company_name_short',
            'registered_office_address',
            'corporate_office_address',
            'cin_no',
            'gst_no',
            'customer_care_email',
            'website_link',
            'emergency_contact',
            'customer_care_phones',
            'logo_image_id',
            'signature_image_id',
            'invoice_text',
            'invoice_image_id'
        ];

        foreach ($columns as $column) {
            $this->forge->dropColumn('bills', $column);
        }

        // Note: Old columns (name, email, address, phone, emergency_no, website) are kept for backward compatibility
        // They are not dropped in down migration to prevent data loss
    }
}
