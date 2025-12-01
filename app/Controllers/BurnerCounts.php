<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\BurnerCountModel;
use App\Models\SettingsModel;

class BurnerCounts extends Controller
{
    public function __construct()
    {
        helper(['url']);
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

        return view('admin/burnercounts/index', [
            'site_logo' => $site_logo,
        ]);
    }

    public function getTableData()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new BurnerCountModel();

        // DataTables parameters
        $draw = $this->request->getGet('draw');
        $start = $this->request->getGet('start') ?? 0;
        $length = $this->request->getGet('length') ?? 10;
        $search = $this->request->getGet('search')['value'] ?? '';
        $orderColumn = $this->request->getGet('order')[0]['column'] ?? 0;
        $orderDir = $this->request->getGet('order')[0]['dir'] ?? 'desc';

        // Column mapping for ordering
        $columns = ['id', 'name', 'status', 'created_date'];
        $orderBy = $columns[$orderColumn] ?? 'id';

        // Build query with admin name join
        $builder = $model->builder();
        $builder->select('burner_counts.*, admins.name as created_by_name, burner_counts.created_date')
                ->join('admins', 'admins.id = burner_counts.created_by', 'left');

        // Apply search
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('burner_counts.name', $search)
                    ->orLike('burner_counts.status', $search)
                    ->groupEnd();
        }

        // Get total records
        $totalRecords = $builder->countAllResults(false);

        // Apply ordering and pagination
        $builder->orderBy($orderBy, $orderDir);
        $builder->limit($length, $start);

        $burnerCounts = $builder->get()->getResultArray();

        $data = [];
        foreach ($burnerCounts as $burnerCount) {
            $adminName = $burnerCount['created_by_name'] ?: 'System';
            $dateTime = $burnerCount['created_date'] ? date('d M Y H:i', strtotime($burnerCount['created_date'])) : 'N/A';
            $createdInfo = '<small><strong>' . esc($adminName) . '</strong><br>' . $dateTime . '</small>';
            $data[] = [
                'id' => $burnerCount['id'],
                'name' => $burnerCount['name'],
                'status' => $burnerCount['status'],
                'created_info' => $createdInfo,
                'actions' => '<button class="btn btn-outline-primary btn-sm" onclick="editBurnerCount(' . $burnerCount['id'] . ')"><i class="fas fa-edit"></i> Edit</button>'
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }

    public function create()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        return $this->response->setJSON([
            'success' => true
        ]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => ['label' => 'Burner Count Name', 'rules' => 'required|min_length[2]|max_length[150]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $model = new BurnerCountModel();
        $data = [
            'name' => $this->request->getPost('name'),
            'status' => $this->request->getPost('status'),
        ];

        if ($model->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Burner count created successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to create burner count'
        ]);
    }

    public function edit($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new BurnerCountModel();
        $burnerCount = $model->find($id);

        if (!$burnerCount) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Burner count not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $burnerCount
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => ['label' => 'Burner Count Name', 'rules' => 'required|min_length[2]|max_length[150]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $model = new BurnerCountModel();
        $data = [
            'name' => $this->request->getPost('name'),
            'status' => $this->request->getPost('status'),
        ];

        if ($model->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Burner count updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update burner count'
        ]);
    }
}
