<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\BillModel;
use App\Models\SettingsModel;
use App\Models\ImageModel;
use App\Libraries\ActivityLogger;

class Bills extends Controller
{
    public function __construct()
    {
        helper(['url', 'form']);
    }

    protected function ensureAuth()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        // Check permission
        if (!hasPermission('masters.view')) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Access denied');
        }

        // Ensure at least one bill is active
        $model = new BillModel();
        $model->ensureActiveBill();

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        return view('admin/bills/index', [
            'site_logo' => $site_logo,
        ]);
    }

    public function getTableData()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        // Build query with created by username
        $db = \Config\Database::connect();
        $bills = $db->table('bills b')
                   ->select('b.*, COALESCE(a.username, a.email) as created_by_name')
                   ->join('admins a', 'a.id = b.created_by', 'left')
                   ->where('b.deleted_at', null)
                   ->orderBy('b.version', 'DESC')
                   ->get()
                   ->getResultArray();

        // Format data for DataTable
        $data = [];
        foreach ($bills as $i => $bill) {
            $statusBadge = '';
            switch ($bill['status']) {
                case 'active':
                    $statusBadge = '<span class="badge badge-success">Active</span>';
                    break;
                case 'draft':
                    $statusBadge = '<span class="badge badge-warning">Draft</span>';
                    break;
                case 'inactive':
                    $statusBadge = '<span class="badge badge-secondary">Inactive</span>';
                    break;
            }

            // Check if this bill can be edited (prevents editing older versions when newer active version exists)
            $canEdit = $this->canEditBill($bill);

            $actions = '';
            if ($canEdit) {
                $actions .= '<a href="' . base_url('admin/bills/edit/' . $bill['id']) . '" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i> Edit</a> ';
                if ($bill['status'] !== 'active') {
                    $actions .= '<button class="btn btn-sm btn-outline-success" onclick="activateBill(' . $bill['id'] . ')"><i class="fas fa-check"></i> Activate</button> ';
                }
            }

            // Add view button for all bills
            $actions .= '<a href="' . base_url('admin/bills/view/' . $bill['id']) . '" class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye"></i> View</a> ';
            $actions .= '<button class="btn btn-sm btn-outline-info" onclick="duplicateBill(' . $bill['id'] . ')"><i class="fas fa-copy"></i> Duplicate</button>';

            $createdInfo = '';
            if ($bill['created_at']) {
                $createdInfo = date('d M Y H:i', strtotime($bill['created_at']));
                if ($bill['created_by_name']) {
                    $createdInfo .= '<br><small class="text-muted">by ' . esc($bill['created_by_name']) . '</small>';
                }
            } else {
                $createdInfo = '-';
            }

            $data[] = [
                'index' => $i + 1,
                'id' => $bill['id'],
                'version' => '<strong>v' . esc($bill['version']) . '</strong>',
                'company_name' => esc($bill['company_name']),
                'status' => $statusBadge,
                'effective_date' => $bill['effective_date'] ? date('d M Y', strtotime($bill['effective_date'])) : '-',
                'created_by' => $createdInfo,
                'actions' => $actions
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($this->request->getGet('draw')),
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
            'data' => $data
        ]);
    }

    public function create()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        // Check permission
        if (!hasPermission('masters.view')) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Access denied');
        }

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        // Get available images for dropdowns
        $imageModel = new ImageModel();
        $images = $imageModel->where('status', 'active')->orderBy('image_name', 'ASC')->findAll();

        return view('admin/bills/create', [
            'site_logo' => $site_logo,
            'images' => $images,
        ]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        // Check permission
        if (!hasPermission('masters.view')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ]);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'company_name' => ['label' => 'Company Name', 'rules' => 'required|min_length[2]|max_length[255]'],
            'company_name_short' => ['label' => 'Company Name Short', 'rules' => 'permit_empty|max_length[100]'],
            'tag_line' => ['label' => 'Tagline', 'rules' => 'permit_empty|max_length[255]'],
            'registered_office_address' => ['label' => 'Registered Office Address', 'rules' => 'permit_empty'],
            'corporate_office_address' => ['label' => 'Corporate Office Address', 'rules' => 'permit_empty'],
            'cin_no' => ['label' => 'CIN No.', 'rules' => 'permit_empty|max_length[50]'],
            'gst_no' => ['label' => 'GST No.', 'rules' => 'permit_empty|max_length[50]'],
            'tin' => ['label' => 'TIN No.', 'rules' => 'permit_empty|max_length[50]'],
            'customer_care_email' => ['label' => 'Customer Care Email', 'rules' => 'required|valid_email|max_length[191]'],
            'website_link' => ['label' => 'Website Link', 'rules' => 'required|valid_url|max_length[255]'],
            'emergency_contact' => ['label' => 'Emergency Contact', 'rules' => 'required|max_length[50]'],
            'customer_care_phones' => ['label' => 'Customer Care Phones', 'rules' => 'required'],
            'effective_date' => ['label' => 'Effective Date', 'rules' => 'permit_empty|valid_date[Y-m-d]'],
            'logo_image_id' => ['label' => 'Logo Image', 'rules' => 'permit_empty|integer'],
            'signature_image_id' => ['label' => 'Signature Image', 'rules' => 'permit_empty|integer'],
            'invoice_text' => ['label' => 'Invoice Text', 'rules' => 'permit_empty'],
            'invoice_image_id' => ['label' => 'Invoice Image', 'rules' => 'permit_empty|integer'],
            'summary_description' => ['label' => 'Summary Description', 'rules' => 'permit_empty'],
            'footer_description' => ['label' => 'Footer Description', 'rules' => 'permit_empty'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $model = new BillModel();

        // Generate version number
        $latestBill = $model->orderBy('version', 'DESC')->first();
        $version = $latestBill ? $model->incrementVersion($latestBill['version']) : '1.0';

        $data = [
            'version' => $version,
            'status' => 'draft', // New bills start as draft
            'effective_date' => $this->request->getPost('effective_date') ?: null,
            'company_name' => $this->request->getPost('company_name'),
            'company_name_short' => $this->request->getPost('company_name_short'),
            'tag_line' => $this->request->getPost('tag_line'),
            'registered_office_address' => $this->request->getPost('registered_office_address'),
            'corporate_office_address' => $this->request->getPost('corporate_office_address'),
            'cin_no' => $this->request->getPost('cin_no'),
            'gst_no' => $this->request->getPost('gst_no'),
            'tin' => $this->request->getPost('tin'),
            'customer_care_email' => $this->request->getPost('customer_care_email'),
            'website_link' => $this->request->getPost('website_link'),
            'emergency_contact' => $this->request->getPost('emergency_contact'),
            'customer_care_phones' => $this->request->getPost('customer_care_phones'),
            'logo_image_id' => $this->request->getPost('logo_image_id') ?: null,
            'signature_image_id' => $this->request->getPost('signature_image_id') ?: null,
            'invoice_text' => $this->request->getPost('invoice_text'),
            'invoice_image_id' => $this->request->getPost('invoice_image_id') ?: null,
            'summary_description' => $this->request->getPost('summary_description'),
            'footer_description' => $this->request->getPost('footer_description'),
        ];

        if ($model->insert($data)) {
            $insertedId = $model->getInsertID();
            // Log add activity
            $activityLogger = new ActivityLogger();
            $activityLogger->logAdd('bills', $insertedId, 'Bill added: ' . $data['company_name'] . ' (v' . $version . ')');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Bill created successfully as draft'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to create bill'
        ]);
    }

    public function view($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        // Get bill with created by name
        $db = \Config\Database::connect();
        $bill = $db->table('bills b')
                  ->select('b.*, COALESCE(a.username, a.email) as created_by_name')
                  ->join('admins a', 'a.id = b.created_by', 'left')
                  ->where('b.id', $id)
                  ->where('b.deleted_at', null)
                  ->get()
                  ->getRowArray();

        if (!$bill) {
            return redirect()->to(base_url('admin/bills'))->with('error', 'Bill not found');
        }

        // Check if this bill can be edited (not editable if newer active version exists)
        $canEdit = $this->canEditBill($bill);

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        // Process shortcodes in bill content
        $bill['summary_description'] = $this->processShortcodes($bill['summary_description']);
        $bill['invoice_text'] = $this->processShortcodes($bill['invoice_text']);
        $bill['footer_description'] = $this->processShortcodes($bill['footer_description']);

        // Get image details if they exist
        $imageModel = new ImageModel();
        $logoImage = $bill['logo_image_id'] ? $imageModel->find($bill['logo_image_id']) : null;
        $signatureImage = $bill['signature_image_id'] ? $imageModel->find($bill['signature_image_id']) : null;
        $invoiceImage = $bill['invoice_image_id'] ? $imageModel->find($bill['invoice_image_id']) : null;

        return view('admin/bills/view', [
            'bill' => $bill,
            'site_logo' => $site_logo,
            'logoImage' => $logoImage,
            'signatureImage' => $signatureImage,
            'invoiceImage' => $invoiceImage,
            'canEdit' => $canEdit,
        ]);
    }

    /**
     * Check if a bill can be edited (only draft bills can be edited)
     */
    private function canEditBill($bill)
    {
        // Only draft bills can be edited
        return $bill['status'] === 'draft';
    }

    /**
     * Process shortcodes in bill content
     */
    private function processShortcodes($content)
    {
        if (!$content) {
            return $content;
        }

        // Get active rate
        $rateModel = new \App\Models\RateModel();
        $activeRate = $rateModel->getActiveRate();

        // Replace shortcodes
        $replacements = [
            '[FULLGASRATE]' => $activeRate ? 'Rs. ' . number_format($activeRate['full_rate'], 2) . '/SCM' : 'Rs. 0.00/SCM',
            '[BASICRATE]' => $activeRate ? 'Rs. ' . number_format($activeRate['basic_rate'], 2) . '/SCM' : 'Rs. 0.00/SCM',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }

    public function edit($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        // Check permission
        if (!hasPermission('masters.view')) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Access denied');
        }

        $model = new BillModel();
        $bill = $model->find($id);
        if (!$bill) {
            return redirect()->to(base_url('admin/bills'))->with('error', 'Bill not found');
        }

        // Check if this bill can be edited
        if (!$this->canEditBill($bill)) {
            return redirect()->to(base_url('admin/bills'))->with('error', 'This bill version cannot be edited because a newer active version exists. Please duplicate this bill to create a draft version, then edit the draft.');
        }

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        // Get available images for dropdowns
        $imageModel = new ImageModel();
        $images = $imageModel->where('status', 'active')->orderBy('image_name', 'ASC')->findAll();

        return view('admin/bills/edit', [
            'bill' => $bill,
            'site_logo' => $site_logo,
            'images' => $images,
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        // Check permission
        if (!hasPermission('masters.view')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ]);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'company_name' => ['label' => 'Company Name', 'rules' => 'required|min_length[2]|max_length[255]'],
            'company_name_short' => ['label' => 'Company Name Short', 'rules' => 'permit_empty|max_length[100]'],
            'tag_line' => ['label' => 'Tagline', 'rules' => 'permit_empty|max_length[255]'],
            'registered_office_address' => ['label' => 'Registered Office Address', 'rules' => 'permit_empty'],
            'corporate_office_address' => ['label' => 'Corporate Office Address', 'rules' => 'permit_empty'],
            'cin_no' => ['label' => 'CIN No.', 'rules' => 'permit_empty|max_length[50]'],
            'gst_no' => ['label' => 'GST No.', 'rules' => 'permit_empty|max_length[50]'],
            'tin' => ['label' => 'TIN No.', 'rules' => 'permit_empty|max_length[50]'],
            'customer_care_email' => ['label' => 'Customer Care Email', 'rules' => 'required|valid_email|max_length[191]'],
            'website_link' => ['label' => 'Website Link', 'rules' => 'required|valid_url|max_length[255]'],
            'emergency_contact' => ['label' => 'Emergency Contact', 'rules' => 'required|max_length[50]'],
            'customer_care_phones' => ['label' => 'Customer Care Phones', 'rules' => 'required'],
            'effective_date' => ['label' => 'Effective Date', 'rules' => 'permit_empty|valid_date[Y-m-d]'],
            'logo_image_id' => ['label' => 'Logo Image', 'rules' => 'permit_empty|integer'],
            'signature_image_id' => ['label' => 'Signature Image', 'rules' => 'permit_empty|integer'],
            'invoice_text' => ['label' => 'Invoice Text', 'rules' => 'permit_empty'],
            'invoice_image_id' => ['label' => 'Invoice Image', 'rules' => 'permit_empty|integer'],
            'summary_description' => ['label' => 'Summary Description', 'rules' => 'permit_empty'],
            'footer_description' => ['label' => 'Footer Description', 'rules' => 'permit_empty'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $model = new BillModel();
        $data = [
            'company_name' => $this->request->getPost('company_name'),
            'company_name_short' => $this->request->getPost('company_name_short'),
            'tag_line' => $this->request->getPost('tag_line'),
            'registered_office_address' => $this->request->getPost('registered_office_address'),
            'corporate_office_address' => $this->request->getPost('corporate_office_address'),
            'cin_no' => $this->request->getPost('cin_no'),
            'gst_no' => $this->request->getPost('gst_no'),
            'tin' => $this->request->getPost('tin'),
            'customer_care_email' => $this->request->getPost('customer_care_email'),
            'website_link' => $this->request->getPost('website_link'),
            'emergency_contact' => $this->request->getPost('emergency_contact'),
            'customer_care_phones' => $this->request->getPost('customer_care_phones'),
            'effective_date' => $this->request->getPost('effective_date') ?: null,
            'logo_image_id' => $this->request->getPost('logo_image_id') ?: null,
            'signature_image_id' => $this->request->getPost('signature_image_id') ?: null,
            'invoice_text' => $this->request->getPost('invoice_text'),
            'invoice_image_id' => $this->request->getPost('invoice_image_id') ?: null,
            'summary_description' => $this->request->getPost('summary_description'),
            'footer_description' => $this->request->getPost('footer_description'),
        ];

        if ($model->update($id, $data)) {
            // Log edit activity
            $activityLogger = new ActivityLogger();
            $activityLogger->logEdit('bills', $id, 'Bill updated: ' . $data['company_name']);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Bill updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update bill'
        ]);
    }

    public function activate($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        // Check permission
        if (!hasPermission('masters.view')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ]);
        }

        $model = new BillModel();
        $bill = $model->find($id);

        if (!$bill) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Bill not found'
            ]);
        }

        // Prevent deactivating an already active bill
        if ($bill['status'] === 'active') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This bill is already active. You cannot deactivate an active bill.'
            ]);
        }

        $effectiveDate = $this->request->getPost('effective_date');

        if ($model->activateBill($id, $effectiveDate)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Bill activated successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to activate bill'
        ]);
    }

    public function duplicate($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        // Check permission
        if (!hasPermission('masters.view')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ]);
        }

        $model = new BillModel();

        if ($model->duplicateBill($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Bill duplicated successfully as draft'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to duplicate bill'
        ]);
    }

    public function getActiveBill()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new BillModel();
        $bill = $model->getActiveBill();

        return $this->response->setJSON([
            'success' => true,
            'data' => $bill
        ]);
    }

    public function getBillByDate()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $date = $this->request->getGet('date');
        if (!$date) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Date parameter required'
            ]);
        }

        $model = new BillModel();
        $bill = $model->getBillByEffectiveDate($date);

        return $this->response->setJSON([
            'success' => true,
            'data' => $bill
        ]);
    }
}