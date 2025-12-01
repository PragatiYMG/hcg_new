<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\MeterContractorModel;
use App\Models\SettingsModel;

class MeterContractors extends Controller
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

        return view('admin/metercontractors/index', [
            'site_logo' => $site_logo,
        ]);
    }

    public function getTableData()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new MeterContractorModel();

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

        // Build query
        $builder = $model->builder();
        $builder->select('*');

        // Apply search
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('name', $search)
                    ->orLike('status', $search)
                    ->groupEnd();
        }

        // Get total records
        $totalRecords = $builder->countAllResults(false);

        // Apply ordering and pagination
        $builder->orderBy($orderBy, $orderDir);
        $builder->limit($length, $start);

        $contractors = $builder->get()->getResultArray();

        $data = [];
        foreach ($contractors as $contractor) {
            $data[] = [
                'id' => $contractor['id'],
                'name' => $contractor['name'],
                'status' => $contractor['status'],
                'created_date' => $contractor['created_date'] ? date('d M Y H:i', strtotime($contractor['created_date'])) : 'N/A',
                'actions' => '<button class="btn btn-outline-primary btn-sm" onclick="editContractor(' . $contractor['id'] . ')"><i class="fas fa-edit"></i> Edit</button>'
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
            'name' => ['label' => 'Contractor Name', 'rules' => 'required|min_length[2]|max_length[150]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $model = new MeterContractorModel();
        $data = [
            'name' => $this->request->getPost('name'),
            'status' => $this->request->getPost('status'),
        ];

        if ($model->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Meter contractor created successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to create meter contractor'
        ]);
    }

    public function edit($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new MeterContractorModel();
        $contractor = $model->find($id);

        if (!$contractor) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Contractor not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $contractor
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => ['label' => 'Contractor Name', 'rules' => 'required|min_length[2]|max_length[150]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $model = new MeterContractorModel();
        $data = [
            'name' => $this->request->getPost('name'),
            'status' => $this->request->getPost('status'),
        ];

        if ($model->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Meter contractor updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update meter contractor'
        ]);
    }
}
