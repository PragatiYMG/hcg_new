<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\StoveTypeModel;
use App\Models\SettingsModel;

class StoveTypes extends Controller
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

        return view('admin/stovetypes/index', [
            'site_logo' => $site_logo,
        ]);
    }

    public function getTableData()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new StoveTypeModel();

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
        $builder->select('stove_types.*, CONCAT(admins.first_name, " ", admins.last_name) as created_by_name, stove_types.created_date')
                ->join('admins', 'admins.id = stove_types.created_by', 'left');

        // Apply search
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('stove_types.name', $search)
                    ->orLike('stove_types.status', $search)
                    ->groupEnd();
        }

        // Get total records
        $totalRecords = $builder->countAllResults(false);

        // Apply ordering and pagination
        $builder->orderBy($orderBy, $orderDir);
        $builder->limit($length, $start);

        $stoveTypes = $builder->get()->getResultArray();

        $data = [];
        foreach ($stoveTypes as $stoveType) {
            $adminName = $stoveType['created_by_name'] ?: 'System';
            $dateTime = $stoveType['created_date'] ? date('d M Y H:i', strtotime($stoveType['created_date'])) : 'N/A';
            $createdInfo = '<small><strong>' . esc($adminName) . '</strong><br>' . $dateTime . '</small>';
            $data[] = [
                'id' => $stoveType['id'],
                'name' => $stoveType['name'],
                'status' => $stoveType['status'],
                'created_info' => $createdInfo,
                'actions' => '<button class="btn btn-outline-primary btn-sm" onclick="editStoveType(' . $stoveType['id'] . ')"><i class="fas fa-edit"></i> Edit</button>'
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
            'name' => ['label' => 'Stove Type Name', 'rules' => 'required|min_length[2]|max_length[150]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $model = new StoveTypeModel();
        $data = [
            'name' => $this->request->getPost('name'),
            'status' => $this->request->getPost('status'),
        ];

        if ($model->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Stove type created successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to create stove type'
        ]);
    }

    public function edit($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new StoveTypeModel();
        $stoveType = $model->find($id);

        if (!$stoveType) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Stove type not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $stoveType
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => ['label' => 'Stove Type Name', 'rules' => 'required|min_length[2]|max_length[150]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $model = new StoveTypeModel();
        $data = [
            'name' => $this->request->getPost('name'),
            'status' => $this->request->getPost('status'),
        ];

        if ($model->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Stove type updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update stove type'
        ]);
    }
}
