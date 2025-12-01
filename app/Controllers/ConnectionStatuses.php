<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ConnectionStatusModel;
use App\Models\SettingsModel;

class ConnectionStatuses extends Controller
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

        return view('admin/connectionstatuses/index', [
            'site_logo' => $site_logo,
        ]);
    }

    public function create()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        // Get the next available order number
        $model = new ConnectionStatusModel();
        $maxOrder = $model->selectMax('status_order')->first();
        $nextOrder = ($maxOrder['status_order'] ?? 0) + 1;

        return $this->response->setJSON([
            'success' => true,
            'next_order' => $nextOrder
        ]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'status_name' => ['label' => 'Status Name', 'rules' => 'required|min_length[2]|max_length[100]'],
            'status_order' => ['label' => 'Status Order', 'rules' => 'required|integer|greater_than[0]|is_unique[connection_statuses.status_order]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $model = new ConnectionStatusModel();
        $data = [
            'status_name' => $this->request->getPost('status_name'),
            'status_order' => $this->request->getPost('status_order'),
        ];

        if ($model->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Connection status created successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to create connection status'
        ]);
    }

    public function getTableData()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new ConnectionStatusModel();
        $statuses = $model->orderBy('status_order', 'ASC')->findAll();

        $data = [];
        foreach ($statuses as $status) {
            $data[] = [
                'index' => $status['status_order'],
                'status_name' => $status['status_name'],
                'status_order' => $status['status_order'],
                'actions' => '<button class="btn btn-outline-primary btn-sm" onclick="editStatus(' . $status['id'] . ')"><i class="fas fa-edit"></i> Edit</button>'
            ];
        }

        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    public function edit($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new ConnectionStatusModel();
        $status = $model->find($id);

        if (!$status) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $status
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'status_name' => ['label' => 'Status Name', 'rules' => 'required|min_length[2]|max_length[100]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $model = new ConnectionStatusModel();
        $data = [
            'status_name' => $this->request->getPost('status_name'),
        ];

        if ($model->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Connection status updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update connection status'
        ]);
    }
}
