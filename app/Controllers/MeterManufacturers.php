<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\MeterManufacturerModel;
use App\Models\SettingsModel;

class MeterManufacturers extends Controller
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

        return view('admin/metermanufacturers/index', [
            'site_logo' => $site_logo,
        ]);
    }

    public function getTableData()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new MeterManufacturerModel();

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
        $builder->select('meter_manufacturers.*, CONCAT(admins.first_name, " ", admins.last_name) as created_by_name, meter_manufacturers.created_date')
                ->join('admins', 'admins.id = meter_manufacturers.created_by', 'left');

        // Apply search
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('meter_manufacturers.name', $search)
                    ->orLike('meter_manufacturers.status', $search)
                    ->groupEnd();
        }

        // Get total records
        $totalRecords = $builder->countAllResults(false);

        // Apply ordering and pagination
        $builder->orderBy($orderBy, $orderDir);
        $builder->limit($length, $start);

        $manufacturers = $builder->get()->getResultArray();

        $data = [];
        foreach ($manufacturers as $manufacturer) {
            $adminName = $manufacturer['created_by_name'] ?: 'System';
            $dateTime = $manufacturer['created_date'] ? date('d M Y H:i', strtotime($manufacturer['created_date'])) : 'N/A';
            $createdInfo = '<small><strong>' . esc($adminName) . '</strong><br>' . $dateTime . '</small>';
            $data[] = [
                'id' => $manufacturer['id'],
                'name' => $manufacturer['name'],
                'status' => $manufacturer['status'],
                'created_info' => $createdInfo,
                'actions' => '<button class="btn btn-outline-primary btn-sm" onclick="editManufacturer(' . $manufacturer['id'] . ')"><i class="fas fa-edit"></i> Edit</button>'
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
            'name' => ['label' => 'Manufacturer Name', 'rules' => 'required|min_length[2]|max_length[150]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $model = new MeterManufacturerModel();
        $data = [
            'name' => $this->request->getPost('name'),
            'status' => $this->request->getPost('status'),
        ];

        if ($model->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Meter manufacturer created successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to create meter manufacturer'
        ]);
    }

    public function edit($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new MeterManufacturerModel();
        $manufacturer = $model->find($id);

        if (!$manufacturer) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Manufacturer not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $manufacturer
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => ['label' => 'Manufacturer Name', 'rules' => 'required|min_length[2]|max_length[150]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $model = new MeterManufacturerModel();
        $data = [
            'name' => $this->request->getPost('name'),
            'status' => $this->request->getPost('status'),
        ];

        if ($model->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Meter manufacturer updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update meter manufacturer'
        ]);
    }
}
