<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\BankModel;
use App\Models\SettingsModel;
use App\Libraries\ActivityLogger;

class Banks extends Controller
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

        // Handle CSV export
        if ($this->request->getGet('export') === 'csv') {
            return $this->exportCsv();
        }

        return view('admin/banks/index', [
            'site_logo' => $site_logo,
        ]);
    }

    public function getTableData()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        // Get filter parameters
        $filters = [
            'created_from' => $this->request->getGet('created_from'),
            'created_to' => $this->request->getGet('created_to'),
            'updated_from' => $this->request->getGet('updated_from'),
            'updated_to' => $this->request->getGet('updated_to'),
        ];

        // Build query with filters
        $db = \Config\Database::connect();
        $query = $db->table('banks b')
                   ->select('b.*, COALESCE(CONCAT(ca.first_name, " ", ca.last_name), ca.username) as created_by_name, COALESCE(CONCAT(ua.first_name, " ", ua.last_name), ua.username) as updated_by_name')
                   ->join('admins ca', 'ca.id = b.created_by', 'left')
                   ->join('admins ua', 'ua.id = b.updated_by', 'left')
                   ->where('b.deleted_at', null);

        // Apply filters
        if (!empty($filters['created_from'])) {
            $query->where('b.created_date >=', $filters['created_from'] . ' 00:00:00');
        }
        if (!empty($filters['created_to'])) {
            $query->where('b.created_date <=', $filters['created_to'] . ' 23:59:59');
        }
        if (!empty($filters['updated_from'])) {
            $query->where('b.updated_date >=', $filters['updated_from'] . ' 00:00:00');
        }
        if (!empty($filters['updated_to'])) {
            $query->where('b.updated_date <=', $filters['updated_to'] . ' 23:59:59');
        }

        $banks = $query->orderBy('b.id', 'DESC')->get()->getResultArray();

        // Format data for DataTable
        $data = [];
        foreach ($banks as $i => $bank) {
            $data[] = [
                'index' => $i + 1,
                'id' => $bank['id'],
                'bank_name' => '<strong>' . esc($bank['bank_name']) . '</strong>',
                'created_by' => '<small><strong>' . esc($bank['created_by_name'] ?? 'Unknown') . '</strong><br>' .
                               (!empty($bank['created_date']) ? date('d M Y H:i', strtotime($bank['created_date'])) : '-') . '</small>',
                'updated_info' => !empty($bank['updated_date']) ?
                    '<small><strong>' . esc($bank['updated_by_name'] ?? 'Unknown') . '</strong><br>' .
                    date('d M Y H:i', strtotime($bank['updated_date'])) . '</small>' :
                    '<small class="text-muted">Never updated</small>',
                'actions' => '<button class="btn btn-sm btn-outline-primary" onclick="editBank(' . $bank['id'] . ')"><i class="fas fa-edit"></i> Edit</button>'
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($this->request->getGet('draw')),
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
            'data' => $data
        ]);
    }


    public function getBanks()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new BankModel();
        $banks = $model->orderBy('id', 'DESC')->findAll();

        // Add admin names for display
        $db = \Config\Database::connect();
        foreach ($banks as &$bank) {
            $createdAdmin = $db->table('admins')->select('name, username')->where('id', $bank['created_by'])->get()->getRowArray();
            $bank['created_by_name'] = $createdAdmin ? ($createdAdmin['name'] ?? $createdAdmin['username']) : 'Unknown';

            if (!empty($bank['updated_by'])) {
                $updatedAdmin = $db->table('admins')->select('name, username')->where('id', $bank['updated_by'])->get()->getRowArray();
                $bank['updated_by_name'] = $updatedAdmin ? ($updatedAdmin['name'] ?? $updatedAdmin['username']) : 'Unknown';
            }
        }

        return $this->response->setJSON(['success' => true, 'data' => $banks]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'bank_name' => ['label' => 'Bank Name', 'rules' => 'required|min_length[2]|max_length[100]|is_unique[banks.bank_name]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $model = new BankModel();
        $data = [
            'bank_name' => trim($this->request->getPost('bank_name')),
        ];

        if ($model->insert($data)) {
            $insertedId = $model->getInsertID();
            // Log add activity
            $activityLogger = new ActivityLogger();
            $activityLogger->logAdd('banks', $insertedId, 'Bank added: ' . $data['bank_name']);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Bank added successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to add bank'
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new BankModel();
        $bank = $model->find($id);

        if (!$bank) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Bank not found'
            ]);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'bank_name' => ['label' => 'Bank Name', 'rules' => "required|min_length[2]|max_length[100]|is_unique[banks.bank_name,id,{$id}]"],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $newBankName = trim($this->request->getPost('bank_name'));

        $data = [
            'bank_name' => $newBankName,
            'updated_date' => date('Y-m-d H:i:s'),
            'updated_by' => session()->get('admin_id')
        ];

        if ($model->update($id, $data)) {
            // Log edit activity
            $activityLogger = new ActivityLogger();
            $activityLogger->logEdit('banks', $id, 'Bank updated: ' . $newBankName);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Bank updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update bank'
        ]);
    }

    public function getBank($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new BankModel();
        $bank = $model->find($id);

        if (!$bank) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Bank not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $bank
        ]);
    }

    public function getBanksForDropdown()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new BankModel();
        $banks = $model->select('id, bank_name')->orderBy('bank_name', 'ASC')->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $banks
        ]);
    }

    private function exportCsv()
    {
        // Get filter parameters
        $filters = [
            'status' => $this->request->getGet('status'),
            'created_from' => $this->request->getGet('created_from'),
            'created_to' => $this->request->getGet('created_to'),
            'updated_from' => $this->request->getGet('updated_from'),
            'updated_to' => $this->request->getGet('updated_to'),
        ];

        // Build query with filters (same as index method)
        $db = \Config\Database::connect();
        $query = $db->table('banks b')
                   ->select('b.*, COALESCE(CONCAT(ca.first_name, " ", ca.last_name), ca.username) as created_by_name, COALESCE(CONCAT(ua.first_name, " ", ua.last_name), ua.username) as updated_by_name')
                   ->join('admins ca', 'ca.id = b.created_by', 'left')
                   ->join('admins ua', 'ua.id = b.updated_by', 'left')
                   ->where('b.deleted_at', null);

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('b.status', $filters['status']);
        }
        if (!empty($filters['created_from'])) {
            $query->where('b.created_date >=', $filters['created_from'] . ' 00:00:00');
        }
        if (!empty($filters['created_to'])) {
            $query->where('b.created_date <=', $filters['created_to'] . ' 23:59:59');
        }
        if (!empty($filters['updated_from'])) {
            $query->where('b.updated_date >=', $filters['updated_from'] . ' 00:00:00');
        }
        if (!empty($filters['updated_to'])) {
            $query->where('b.updated_date <=', $filters['updated_to'] . ' 23:59:59');
        }

        $banks = $query->orderBy('b.id', 'DESC')->get()->getResultArray();

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="banks_' . date('Y-m-d_H-i-s') . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Write CSV headers
        fputcsv($output, [
            'ID',
            'Bank Name',
            'Created Date',
            'Created By',
            'Last Updated',
            'Updated By'
        ]);

        // Write data rows
        foreach ($banks as $bank) {
            fputcsv($output, [
                $bank['id'],
                $bank['bank_name'],
                $bank['created_date'] ? date('d M Y H:i', strtotime($bank['created_date'])) : 'N/A',
                $bank['created_by_name'] ?? 'Unknown',
                $bank['updated_date'] ? date('d M Y H:i', strtotime($bank['updated_date'])) : 'Never updated',
                $bank['updated_by_name'] ?? 'N/A'
            ]);
        }

        fclose($output);
        exit();
    }
}
