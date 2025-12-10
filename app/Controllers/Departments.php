<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\DepartmentModel;
use App\Models\SettingsModel;
use App\Libraries\ActivityLogger;

class Departments extends Controller
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
        // Only super admin can access departments
        if (session()->get('admin_role') !== 'super_admin') {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Access denied. Only Super Admin can access this section.');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        return view('admin/departments/index', [
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
        $query = $db->table('departments d')
                   ->select('d.*, COALESCE(CONCAT(ca.first_name, " ", ca.last_name), ca.username) as created_by_name, COALESCE(CONCAT(ua.first_name, " ", ua.last_name), ua.username) as updated_by_name')
                   ->join('admins ca', 'ca.id = d.created_by', 'left')
                   ->join('admins ua', 'ua.id = d.updated_by', 'left')
                   ->where('d.deleted_at', null);

        // Apply filters
        if (!empty($filters['created_from'])) {
            $query->where('d.created_date >=', $filters['created_from'] . ' 00:00:00');
        }
        if (!empty($filters['created_to'])) {
            $query->where('d.created_date <=', $filters['created_to'] . ' 23:59:59');
        }
        if (!empty($filters['updated_from'])) {
            $query->where('d.updated_at >=', $filters['updated_from'] . ' 00:00:00');
        }
        if (!empty($filters['updated_to'])) {
            $query->where('d.updated_at <=', $filters['updated_to'] . ' 23:59:59');
        }

        $departments = $query->orderBy('d.id', 'DESC')->get()->getResultArray();

        // Format data for DataTable
        $data = [];
        foreach ($departments as $i => $dept) {
            $data[] = [
                'index' => $i + 1,
                'id' => $dept['id'],
                'department_name' => '<strong>' . esc($dept['department_name']) . '</strong>',
                'status' => $dept['status'] === 'active' ?
                    '<span class="badge badge-success">Active</span>' :
                    '<span class="badge badge-secondary">Inactive</span>',
                'created_by' => '<small><strong>' . esc($dept['created_by_name'] ?? 'Unknown') . '</strong><br>' .
                               (!empty($dept['created_date']) ? date('d M Y H:i', strtotime($dept['created_date'])) : '-') . '</small>',
                'updated_info' => !empty($dept['updated_at']) ?
                    '<small><strong>' . esc($dept['updated_by_name'] ?? 'Unknown') . '</strong><br>' .
                    date('d M Y H:i', strtotime($dept['updated_at'])) . '</small>' :
                    '<small class="text-muted">Never updated</small>',
                'actions' => '<button class="btn btn-sm btn-outline-primary" onclick="editDepartment(' . $dept['id'] . ')"><i class="fas fa-edit"></i> Edit</button>'
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($this->request->getGet('draw')),
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
            'data' => $data
        ]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'department_name' => ['label' => 'Department Name', 'rules' => 'required|min_length[2]|max_length[255]|is_unique[departments.department_name]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $model = new DepartmentModel();
        $data = [
            'department_name' => trim($this->request->getPost('department_name')),
            'status' => $this->request->getPost('status'),
        ];

        if ($model->insert($data)) {
            $insertedId = $model->getInsertID();
            // Log add activity
            $activityLogger = new ActivityLogger();
            $activityLogger->logAdd('departments', $insertedId, 'Department added: ' . $data['department_name']);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Department added successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to add department'
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new DepartmentModel();
        $department = $model->find($id);

        if (!$department) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Department not found'
            ]);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'department_name' => ['label' => 'Department Name', 'rules' => "required|min_length[2]|max_length[255]|is_unique[departments.department_name,id,{$id}]"],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $newDepartmentName = trim($this->request->getPost('department_name'));

        $data = [
            'department_name' => $newDepartmentName,
            'status' => $this->request->getPost('status'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => session()->get('admin_id')
        ];

        if ($model->update($id, $data)) {
            // Log edit activity
            $activityLogger = new ActivityLogger();
            $activityLogger->logEdit('departments', $id, 'Department updated: ' . $newDepartmentName);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Department updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update department'
        ]);
    }

    public function getDepartment($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new DepartmentModel();
        $department = $model->find($id);

        if (!$department) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Department not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $department
        ]);
    }
}