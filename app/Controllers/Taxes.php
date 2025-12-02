<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\TaxModel;
use App\Models\SettingsModel;
use App\Libraries\ActivityLogger;

class Taxes extends Controller
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

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        return view('admin/taxes/index', ['site_logo' => $site_logo]);
    }

    public function getTaxes()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new TaxModel();
        $taxes = $model->orderBy('id', 'DESC')->findAll();

        // Add admin names for display
        $db = \Config\Database::connect();
        foreach ($taxes as &$tax) {
            $admin = $db->table('admins')->select('name, username')->where('id', $tax['created_by'])->get()->getRowArray();
            $tax['created_by_name'] = $admin ? ($admin['name'] ?? $admin['username']) : 'Unknown';

            if (!empty($tax['updated_at'])) {
                $updatedAdmin = $db->table('admins')->select('name, username')->where('id', $tax['updated_by'] ?? null)->get()->getRowArray();
                $tax['updated_by_name'] = $updatedAdmin ? ($updatedAdmin['name'] ?? $updatedAdmin['username']) : 'Unknown';
            }
        }

        return $this->response->setJSON(['success' => true, 'data' => $taxes]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'type_name' => [
                'label' => 'Tax Name',
                'rules' => 'required|min_length[2]|max_length[150]|is_unique[tax_types.type_name]',
                'errors' => [
                    'required' => 'Tax name is required',
                    'min_length' => 'Tax name must be at least 2 characters',
                    'max_length' => 'Tax name cannot exceed 150 characters',
                    'is_unique' => 'This tax name already exists'
                ]
            ],
            'tax_rate' => [
                'label' => 'Tax Rate',
                'rules' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
                'errors' => [
                    'required' => 'Tax rate is required',
                    'decimal' => 'Tax rate must be a valid decimal number',
                    'greater_than_equal_to' => 'Tax rate cannot be negative',
                    'less_than_equal_to' => 'Tax rate cannot exceed 100%'
                ]
            ],
            'status' => [
                'label' => 'Status',
                'rules' => 'required|in_list[active,inactive]',
                'errors' => [
                    'required' => 'Status is required',
                    'in_list' => 'Status must be either active or inactive'
                ]
            ],
            'online_status' => [
                'label' => 'Online Status',
                'rules' => 'required|in_list[online,offline]',
                'errors' => [
                    'required' => 'Online status is required',
                    'in_list' => 'Online status must be either online or offline'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $model = new TaxModel();
        $data = [
            'type_name' => $this->request->getPost('type_name'),
            'tax_rate' => $this->request->getPost('tax_rate'),
            'status' => $this->request->getPost('status'),
            'online_status' => $this->request->getPost('online_status'),
        ];

        if ($model->insert($data)) {
            $insertedId = $model->getInsertID();
            // Log add activity
            $activityLogger = new ActivityLogger();
            $activityLogger->logAdd('tax_types', $insertedId, 'Tax added: ' . $data['type_name'] . ' (' . $data['tax_rate'] . '%)');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tax created successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to create tax'
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new TaxModel();
        $tax = $model->find($id);

        if (!$tax) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tax not found'
            ]);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'type_name' => [
                'label' => 'Tax Name',
                'rules' => "required|min_length[2]|max_length[150]|is_unique[tax_types.type_name,id,{$id}]",
                'errors' => [
                    'required' => 'Tax name is required',
                    'min_length' => 'Tax name must be at least 2 characters',
                    'max_length' => 'Tax name cannot exceed 150 characters',
                    'is_unique' => 'This tax name already exists'
                ]
            ],
            'tax_rate' => [
                'label' => 'Tax Rate',
                'rules' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
                'errors' => [
                    'required' => 'Tax rate is required',
                    'decimal' => 'Tax rate must be a valid decimal number',
                    'greater_than_equal_to' => 'Tax rate cannot be negative',
                    'less_than_equal_to' => 'Tax rate cannot exceed 100%'
                ]
            ],
            'status' => [
                'label' => 'Status',
                'rules' => 'required|in_list[active,inactive]',
                'errors' => [
                    'required' => 'Status is required',
                    'in_list' => 'Status must be either active or inactive'
                ]
            ],
            'online_status' => [
                'label' => 'Online Status',
                'rules' => 'required|in_list[online,offline]',
                'errors' => [
                    'required' => 'Online status is required',
                    'in_list' => 'Online status must be either online or offline'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        // Check if tax rate is being changed and show caution
        $newRate = $this->request->getPost('tax_rate');
        $cautionMessage = '';
        if ($tax['tax_rate'] != $newRate) {
            $cautionMessage = "⚠️ CAUTION: Tax rate changed from {$tax['tax_rate']}% to {$newRate}%. This will affect all billing calculations.";
        }

        $data = [
            'type_name' => $this->request->getPost('type_name'),
            'tax_rate' => $newRate,
            'status' => $this->request->getPost('status'),
            'online_status' => $this->request->getPost('online_status'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => session()->get('admin_id')
        ];

        if ($model->update($id, $data)) {
            // Log edit activity
            $activityLogger = new ActivityLogger();
            $activityLogger->logEdit('tax_types', $id, 'Tax updated: ' . $data['type_name'] . ' (' . $data['tax_rate'] . '%)');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tax updated successfully' . ($cautionMessage ? '. ' . $cautionMessage : ''),
                'caution' => $cautionMessage
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update tax'
        ]);
    }
}
