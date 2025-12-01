<?php

namespace App\Models;

use CodeIgniter\Model;

class BillModel extends Model
{
    protected $table            = 'bills';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        // Versioning fields
        'version',
        'effective_date',
        'status',

        // Company information
        'company_name',
        'company_name_short',
        'tag_line',

        // Addresses
        'registered_office_address',
        'corporate_office_address',

        // Registration numbers
        'cin_no',
        'gst_no',
        'tin',

        // Contact information
        'customer_care_phones',
        'emergency_contact',
        'customer_care_email',
        'website_link',

        // Image references
        'logo_image_id',
        'signature_image_id',
        'invoice_image_id',

        // Content
        'summary_description',
        'footer_description',
        'invoice_text',

        // Audit fields
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    protected bool $allowEmptyInserts = false;

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedFields'];
    protected $beforeUpdate   = ['setUpdatedFields'];

    protected function setCreatedFields(array $data)
    {
        $data['data']['created_by'] = session()->get('admin_id');
        $data['data']['created_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    protected function setUpdatedFields(array $data)
    {
        $data['data']['updated_by'] = session()->get('admin_id');
        $data['data']['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    /**
     * Get the currently active bill
     */
    public function getActiveBill()
    {
        return $this->where('status', 'active')->first();
    }

    /**
     * Get all bill versions ordered by version desc
     */
    public function getBillVersions()
    {
        return $this->orderBy('version', 'DESC')->findAll();
    }

    /**
     * Deactivate all bills except the specified one
     */
    public function deactivateAllExcept($billId)
    {
        return $this->where('id !=', $billId)
                   ->where('status', 'active')
                   ->set(['status' => 'inactive'])
                   ->update();
    }

    /**
     * Create a duplicate bill as draft
     */
    public function duplicateBill($billId)
    {
        $originalBill = $this->find($billId);
        if (!$originalBill) {
            return false;
        }

        // Remove id and audit fields for duplication
        unset($originalBill['id']);
        unset($originalBill['created_at']);
        unset($originalBill['created_by']);
        unset($originalBill['updated_at']);
        unset($originalBill['updated_by']);
        unset($originalBill['deleted_at']);

        // Set as draft and increment version
        $originalBill['status'] = 'draft';
        $originalBill['version'] = $this->incrementVersion($originalBill['version']);
        $originalBill['effective_date'] = null;

        return $this->insert($originalBill);
    }

    /**
     * Increment version number (e.g., 1.0 -> 1.1, 1.9 -> 2.0)
     */
    private function incrementVersion($currentVersion)
    {
        $parts = explode('.', $currentVersion);
        $major = (int)($parts[0] ?? 1);
        $minor = (int)($parts[1] ?? 0);

        $minor++;
        if ($minor >= 10) {
            $major++;
            $minor = 0;
        }

        return $major . '.' . $minor;
    }

    /**
     * Activate a bill and deactivate others
     */
    public function activateBill($billId, $effectiveDate = null)
    {
        // First deactivate all active bills
        $this->deactivateAllExcept($billId);

        // Then activate the specified bill
        $data = ['status' => 'active'];
        if ($effectiveDate) {
            $data['effective_date'] = $effectiveDate;
        }

        return $this->update($billId, $data);
    }

    /**
     * Get bill by effective date (for historical invoices)
     */
    public function getBillByEffectiveDate($date)
    {
        return $this->where('effective_date <=', $date)
                   ->where('status', 'active')
                   ->orderBy('effective_date', 'DESC')
                   ->first();
    }

    /**
     * Ensure at least one bill is active (set the latest version as active if none exists)
     */
    public function ensureActiveBill()
    {
        $activeBill = $this->getActiveBill();
        if (!$activeBill) {
            // Find the latest bill and make it active
            $latestBill = $this->orderBy('version', 'DESC')->first();
            if ($latestBill) {
                $this->update($latestBill['id'], ['status' => 'active']);
                return $latestBill['id'];
            }
        }
        return $activeBill ? $activeBill['id'] : null;
    }
}