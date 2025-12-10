<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\SocietyModel;
use App\Models\AreaModel;
use App\Models\SettingsModel;
use App\Libraries\ActivityLogger;

class Societies extends Controller
{
    public function __construct()
    {
        helper('url');
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

        $model = new SocietyModel();
        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        // Handle CSV export
        if ($this->request->getGet('export') === 'csv') {
            return $this->exportCsv();
        }

        // Get filter parameters
        $filters = [
            'area_id' => $this->request->getGet('area_id'),
            'status' => $this->request->getGet('status'),
            'visible_to_customer' => $this->request->getGet('visible_to_customer'),
            'created_from' => $this->request->getGet('created_from'),
            'created_to' => $this->request->getGet('created_to'),
            'updated_from' => $this->request->getGet('updated_from'),
            'updated_to' => $this->request->getGet('updated_to'),
        ];

        // Build query with filters
        $db = \Config\Database::connect();
        $query = $db->table('societies s')
                   ->select('s.*, a.area_name, COALESCE(CONCAT(ca.first_name, " ", ca.last_name), ca.username) as created_by_name, COALESCE(CONCAT(ua.first_name, " ", ua.last_name), ua.username) as updated_by_name')
                   ->join('areas a', 'a.id = s.area_id', 'left')
                   ->join('admins ca', 'ca.id = s.created_by', 'left')
                   ->join('admins ua', 'ua.id = s.updated_by', 'left')
                   ->where('s.deleted_at', null);

        // Apply filters
        if (!empty($filters['area_id'])) {
            $query->where('s.area_id', $filters['area_id']);
        }
        if (!empty($filters['status'])) {
            $query->where('s.status', $filters['status']);
        }
        if (!empty($filters['visible_to_customer'])) {
            $query->where('s.visible_to_customer', $filters['visible_to_customer']);
        }
        if (!empty($filters['created_from'])) {
            $query->where('s.created_date >=', $filters['created_from'] . ' 00:00:00');
        }
        if (!empty($filters['created_to'])) {
            $query->where('s.created_date <=', $filters['created_to'] . ' 23:59:59');
        }
        if (!empty($filters['updated_from'])) {
            $query->where('s.updated_at >=', $filters['updated_from'] . ' 00:00:00');
        }
        if (!empty($filters['updated_to'])) {
            $query->where('s.updated_at <=', $filters['updated_to'] . ' 23:59:59');
        }

        $societies = $query->orderBy('s.id', 'DESC')->get()->getResultArray();

        // Get areas for filter dropdown
        $areaModel = new AreaModel();
        $areas = $areaModel->where('status', 'active')->orderBy('area_name', 'ASC')->findAll();

        return view('admin/societies/index', [
            'societies' => $societies,
            'site_logo' => $site_logo,
            'areas' => $areas,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        $areaModel = new AreaModel();
        $areas = $areaModel->where('status', 'active')->orderBy('area_name', 'ASC')->findAll();

        return view('admin/societies/create', [
            'site_logo' => $site_logo,
            'areas' => $areas,
        ]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new SocietyModel();

        // Check for duplicate society names (case-insensitive)
        $societyName = $this->request->getPost('society_name');
        $existingSociety = $model->where('LOWER(society_name)', strtolower($societyName))->first();
        if ($existingSociety) {
            return redirect()->back()->withInput()->with('error', 'A society with this name already exists (case-insensitive match).');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'area_id' => ['label' => 'Area', 'rules' => 'required|integer|is_not_unique[areas.id]'],
            'society_name' => ['label' => 'Society Name', 'rules' => 'required|min_length[2]|max_length[200]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
            'visible_to_customer' => ['label' => 'Visible to Customer', 'rules' => 'required|in_list[yes,no]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'area_id' => (int)$this->request->getPost('area_id'),
            'society_name' => $this->request->getPost('society_name'),
            'status' => $this->request->getPost('status'),
            'visible_to_customer' => $this->request->getPost('visible_to_customer'),
        ];

        if ($model->insert($data)) {
            $insertedId = $model->getInsertID();
            // Log add activity
            $activityLogger = new ActivityLogger();
            $activityLogger->logAdd('societies', $insertedId, 'Society added: ' . $data['society_name']);

            return redirect()->to(base_url('admin/societies'))->with('success', 'Society created successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create society');
    }

    public function edit($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new SocietyModel();
        $society = $model->find($id);
        if (!$society) {
            return redirect()->to(base_url('admin/societies'))->with('error', 'Society not found');
        }

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        $areaModel = new AreaModel();
        $areas = $areaModel->where('status', 'active')->orderBy('area_name', 'ASC')->findAll();

        return view('admin/societies/edit', [
            'society' => $society,
            'site_logo' => $site_logo,
            'areas' => $areas,
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new SocietyModel();

        // Check for duplicate society names (case-insensitive, excluding current record)
        $societyName = $this->request->getPost('society_name');
        $existingSociety = $model->where('LOWER(society_name)', strtolower($societyName))
                                 ->where('id !=', $id)
                                 ->first();
        if ($existingSociety) {
            return redirect()->back()->withInput()->with('error', 'A society with this name already exists (case-insensitive match).');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'area_id' => ['label' => 'Area', 'rules' => 'required|integer|is_not_unique[areas.id]'],
            'society_name' => ['label' => 'Society Name', 'rules' => 'required|min_length[2]|max_length[200]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
            'visible_to_customer' => ['label' => 'Visible to Customer', 'rules' => 'required|in_list[yes,no]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'area_id' => (int)$this->request->getPost('area_id'),
            'society_name' => $this->request->getPost('society_name'),
            'status' => $this->request->getPost('status'),
            'visible_to_customer' => $this->request->getPost('visible_to_customer'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($model->update($id, $data)) {
            // Log edit activity
            $activityLogger = new ActivityLogger();
            $activityLogger->logEdit('societies', $id, 'Society updated: ' . $data['society_name']);

            return redirect()->to(base_url('admin/societies'))->with('success', 'Society updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update society');
    }

    public function delete($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new SocietyModel();
        if ($model->delete($id)) {
            return redirect()->to(base_url('admin/societies'))->with('success', 'Society deleted successfully');
        }
        return redirect()->to(base_url('admin/societies'))->with('error', 'Failed to delete society');
    }

    private function exportCsv()
    {
        // Get filter parameters
        $filters = [
            'area_id' => $this->request->getGet('area_id'),
            'status' => $this->request->getGet('status'),
            'visible_to_customer' => $this->request->getGet('visible_to_customer'),
            'created_from' => $this->request->getGet('created_from'),
            'created_to' => $this->request->getGet('created_to'),
            'updated_from' => $this->request->getGet('updated_from'),
            'updated_to' => $this->request->getGet('updated_to'),
        ];

        // Build query with filters (same as index method)
        $db = \Config\Database::connect();
        $query = $db->table('societies s')
                   ->select('s.*, a.area_name, COALESCE(CONCAT(ca.first_name, " ", ca.last_name), ca.username) as created_by_name, COALESCE(CONCAT(ua.first_name, " ", ua.last_name), ua.username) as updated_by_name')
                   ->join('areas a', 'a.id = s.area_id', 'left')
                   ->join('admins ca', 'ca.id = s.created_by', 'left')
                   ->join('admins ua', 'ua.id = s.updated_by', 'left')
                   ->where('s.deleted_at', null);

        // Apply filters
        if (!empty($filters['area_id'])) {
            $query->where('s.area_id', $filters['area_id']);
        }
        if (!empty($filters['status'])) {
            $query->where('s.status', $filters['status']);
        }
        if (!empty($filters['visible_to_customer'])) {
            $query->where('s.visible_to_customer', $filters['visible_to_customer']);
        }
        if (!empty($filters['created_from'])) {
            $query->where('s.created_date >=', $filters['created_from'] . ' 00:00:00');
        }
        if (!empty($filters['created_to'])) {
            $query->where('s.created_date <=', $filters['created_to'] . ' 23:59:59');
        }
        if (!empty($filters['updated_from'])) {
            $query->where('s.updated_at >=', $filters['updated_from'] . ' 00:00:00');
        }
        if (!empty($filters['updated_to'])) {
            $query->where('s.updated_at <=', $filters['updated_to'] . ' 23:59:59');
        }

        $societies = $query->orderBy('s.id', 'DESC')->get()->getResultArray();

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="societies_' . date('Y-m-d_H-i-s') . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Write CSV headers
        fputcsv($output, [
            'ID',
            'Society Name',
            'Area',
            'Status',
            'Visible to Customer',
            'Created Date',
            'Created By',
            'Last Updated',
            'Updated By'
        ]);

        // Write data rows
        foreach ($societies as $society) {
            fputcsv($output, [
                $society['id'],
                $society['society_name'],
                $society['area_name'] ?? 'N/A',
                ucfirst($society['status']),
                ucfirst($society['visible_to_customer']),
                $society['created_date'] ? date('d M Y H:i', strtotime($society['created_date'])) : 'N/A',
                $society['created_by_name'] ?? 'Unknown',
                $society['updated_at'] ? date('d M Y H:i', strtotime($society['updated_at'])) : 'Never updated',
                $society['updated_by_name'] ?? 'N/A'
            ]);
        }

        fclose($output);
        exit();
    }
}