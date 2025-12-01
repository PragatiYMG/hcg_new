<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\SettingsModel;
use App\Models\AreaModel;
use CodeIgniter\Controller;

class Admin extends Controller
{
    public function __construct()
    {
        helper('url');
    }
    public function login()
    {
        if (session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/dashboard'));
        }

        return view('admin/login');
    }

    public function authenticate()
    {
        $model = new AdminModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $admin = $model->where('email', $email)->first();

        if ($admin && password_verify($password, $admin['password'])) {
            session()->set([
                'admin_id' => $admin['id'],
                'admin_username' => $admin['username'],
                'admin_logged_in' => true
            ]);

            return redirect()->to(base_url('admin/dashboard'));
        } else {
            session()->setFlashdata('error', 'Invalid username or password');
            return redirect()->back()->withInput();
        }
    }

    public function dashboard()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        return view('admin/dashboard', ['site_logo' => $site_logo]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('admin/login'));
    }

    public function settings()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');
        $site_favicon = $settingsModel->getSetting('site_favicon');

        return view('admin/settings', ['site_logo' => $site_logo, 'site_favicon' => $site_favicon]);
    }

    public function updateSettings()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        $settingsModel = new SettingsModel();



        $validation = \Config\Services::validation();
        $rules = [];
        
        // Only validate logo if it was uploaded
        if ($this->request->getFile('logo')->isValid()) {
            $rules['logo'] = [
                'label' => 'Logo',
                'rules' => 'max_size[logo,2048]|is_image[logo]|mime_in[logo,image/jpg,image/jpeg,image/png,image/webp]',
                'errors' => [
                    'max_size' => 'Logo image size should not exceed 2MB',
                    'is_image' => 'Please upload a valid image file',
                    'mime_in' => 'Only JPG, JPEG, PNG and WebP images are allowed'
                ]
            ];
        }
        
        // Only validate favicon if it was uploaded
        if ($this->request->getFile('favicon')->isValid()) {
            $rules['favicon'] = [
                'label' => 'Favicon',
                'rules' => 'max_size[favicon,1024]|is_image[favicon]|mime_in[favicon,image/x-icon,image/vnd.microsoft.icon,image/png]',
                'errors' => [
                    'max_size' => 'Favicon size should not exceed 1MB',
                    'is_image' => 'Please upload a valid favicon',
                    'mime_in' => 'Only ICO, PNG, or JPG images are allowed for favicon'
                ]
            ];
        }
        
        // Only run validation if there are rules to validate
        if (!empty($rules)) {
            $validation->setRules($rules);
            if (!$validation->withRequest($this->request)->run()) {
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }
        }

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $logo = $this->request->getFile('logo');
        $favicon = $this->request->getFile('favicon');
        $uploadPath = ROOTPATH . 'uploads/';
        
        // Create uploads directory if it doesn't exist
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Handle logo upload
        if ($logo->isValid() && !$logo->hasMoved()) {
            // Delete old logo if exists
            $oldLogo = $settingsModel->getSetting('site_logo');
            if ($oldLogo && file_exists(ROOTPATH . 'uploads/' . $oldLogo)) {
                unlink(ROOTPATH . 'uploads/' . $oldLogo);
            }
            
            $newLogoName = 'logo_' . time() . '.' . $logo->getClientExtension();
            $logo->move($uploadPath, $newLogoName);
            $settingsModel->setSetting('site_logo', $newLogoName);
        }

        // Handle favicon upload
        if ($favicon->isValid() && !$favicon->hasMoved()) {
            // Delete old favicon if exists
            $oldFavicon = $settingsModel->getSetting('site_favicon');
            if ($oldFavicon && file_exists(ROOTPATH . 'uploads/' . $oldFavicon)) {
                unlink(ROOTPATH . 'uploads/' . $oldFavicon);
            }
            
            $newFaviconName = 'favicon_' . time() . '.' . ($favicon->getClientExtension() === 'ico' ? 'ico' : 'png');
            $favicon->move($uploadPath, $newFaviconName);
            $settingsModel->setSetting('site_favicon', $newFaviconName);
        }
        return redirect()->to(base_url('admin/settings'))->with('success', 'Settings updated successfully');
    }

    public function areas()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        // Handle CSV export
        if ($this->request->getGet('export') === 'csv') {
            return $this->exportAreasCsv();
        }

        // Get filter parameters
        $filters = [
            'area_name' => $this->request->getGet('area_name'),
            'status' => $this->request->getGet('status'),
            'visible_to_customer' => $this->request->getGet('visible_to_customer'),
            'created_from' => $this->request->getGet('created_from'),
            'created_to' => $this->request->getGet('created_to'),
        ];

        // Build query with filters and society count
        $db = \Config\Database::connect();
        $query = $db->table('areas a')
                    ->select('a.*, COALESCE(ca.name, ca.username) as created_by_name, COALESCE(ua.name, ua.username) as updated_by_name, COUNT(s.id) as society_count')
                    ->join('admins ca', 'ca.id = a.created_by', 'left')
                    ->join('admins ua', 'ua.id = a.updated_by', 'left')
                    ->join('societies s', 's.area_id = a.id AND s.deleted_at IS NULL', 'left')
                    ->where('a.deleted_at', null)
                    ->groupBy('a.id');

        // Apply filters
        if (!empty($filters['area_name'])) {
            $query->like('a.area_name', $filters['area_name']);
        }
        if (!empty($filters['status'])) {
            $query->where('a.status', $filters['status']);
        }
        if (!empty($filters['visible_to_customer'])) {
            $query->where('a.visible_to_customer', $filters['visible_to_customer']);
        }
        if (!empty($filters['created_from'])) {
            $query->where('a.created_date >=', $filters['created_from'] . ' 00:00:00');
        }
        if (!empty($filters['created_to'])) {
            $query->where('a.created_date <=', $filters['created_to'] . ' 23:59:59');
        }

        $areas = $query->orderBy('a.id', 'DESC')->get()->getResultArray();

        return view('admin/areas/index', ['areas' => $areas, 'filters' => $filters]);
    }

    public function createArea()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        return view('admin/areas/create');
    }

    public function storeArea()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        $areaModel = new AreaModel();

        // Check for duplicates and partial matches
        $areaName = $this->request->getPost('area_name');
        $areaModel = new AreaModel();

        // Check for exact case-insensitive match
        $existingArea = $areaModel->where('LOWER(area_name)', strtolower($areaName))->first();
        if ($existingArea) {
            return redirect()->back()->withInput()->with('error', 'An area with this name already exists (case-insensitive match).');
        }

        // Check for partial matches (similar names)
        $partialMatches = $areaModel->like('area_name', $areaName, 'both')->findAll();
        if (!empty($partialMatches)) {
            $matchNames = array_column($partialMatches, 'area_name');
            session()->setFlashdata('warning', 'Similar area names found: ' . implode(', ', $matchNames) . '. Do you want to proceed?');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'area_name' => [
                'label' => 'Area Name',
                'rules' => 'required|min_length[2]|max_length[255]',
                'errors' => [
                    'required' => 'Area name is required',
                    'min_length' => 'Area name must be at least 2 characters',
                    'max_length' => 'Area name cannot exceed 255 characters'
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
            'visible_to_customer' => [
                'label' => 'Visible to Customer',
                'rules' => 'required|in_list[yes,no]',
                'errors' => [
                    'required' => 'Visible to customer is required',
                    'in_list' => 'Visible to customer must be either yes or no'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'area_name' => $this->request->getPost('area_name'),
            'status' => $this->request->getPost('status'),
            'visible_to_customer' => $this->request->getPost('visible_to_customer')
        ];

        if ($areaModel->insert($data)) {
            return redirect()->to(base_url('admin/areas'))->with('success', 'Area created successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create area');
        }
    }

    public function editArea($id)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        $areaModel = new AreaModel();
        $area = $areaModel->find($id);

        if (!$area) {
            return redirect()->to(base_url('admin/areas'))->with('error', 'Area not found');
        }

        return view('admin/areas/edit', ['area' => $area]);
    }

    public function updateArea($id)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        $areaModel = new AreaModel();

        // Check for duplicates and partial matches (excluding current record)
        $areaName = $this->request->getPost('area_name');
        $newStatus = $this->request->getPost('status');

        // Check if trying to disable area with active societies
        if ($newStatus === 'inactive') {
            $societyModel = new \App\Models\SocietyModel();
            $activeSocieties = $societyModel->where('area_id', $id)
                                           ->where('status', 'active')
                                           ->countAllResults();
            if ($activeSocieties > 0) {
                return redirect()->back()->withInput()->with('error', 'Cannot disable this area because it has ' . $activeSocieties . ' active society(ies). Please disable or move the societies first.');
            }
        }

        // Check for exact case-insensitive match (excluding current record)
        $existingArea = $areaModel->where('LOWER(area_name)', strtolower($areaName))
                                  ->where('id !=', $id)
                                  ->first();
        if ($existingArea) {
            return redirect()->back()->withInput()->with('error', 'An area with this name already exists (case-insensitive match).');
        }

        // Check for partial matches (excluding current record)
        $partialMatches = $areaModel->like('area_name', $areaName, 'both')
                                    ->where('id !=', $id)
                                    ->findAll();
        if (!empty($partialMatches)) {
            $matchNames = array_column($partialMatches, 'area_name');
            session()->setFlashdata('warning', 'Similar area names found: ' . implode(', ', $matchNames) . '. Do you want to proceed?');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'area_name' => [
                'label' => 'Area Name',
                'rules' => 'required|min_length[2]|max_length[255]',
                'errors' => [
                    'required' => 'Area name is required',
                    'min_length' => 'Area name must be at least 2 characters',
                    'max_length' => 'Area name cannot exceed 255 characters'
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
            'visible_to_customer' => [
                'label' => 'Visible to Customer',
                'rules' => 'required|in_list[yes,no]',
                'errors' => [
                    'required' => 'Visible to customer is required',
                    'in_list' => 'Visible to customer must be either yes or no'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'area_name' => $this->request->getPost('area_name'),
            'status' => $this->request->getPost('status'),
            'visible_to_customer' => $this->request->getPost('visible_to_customer')
        ];

        if ($areaModel->update($id, $data)) {
            return redirect()->to(base_url('admin/areas'))->with('success', 'Area updated successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update area');
        }
    }

    public function deleteArea($id)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        $areaModel = new AreaModel();

        if ($areaModel->delete($id)) {
            return redirect()->to(base_url('admin/areas'))->with('success', 'Area deleted successfully');
        } else {
            return redirect()->to(base_url('admin/areas'))->with('error', 'Failed to delete area');
        }
    }

    private function exportAreasCsv()
        {
            // Get filter parameters
            $filters = [
                'area_name' => $this->request->getGet('area_name'),
                'status' => $this->request->getGet('status'),
                'visible_to_customer' => $this->request->getGet('visible_to_customer'),
                'created_from' => $this->request->getGet('created_from'),
                'created_to' => $this->request->getGet('created_to'),
            ];
    
            // Build query with filters and society count (same as areas method)
            $db = \Config\Database::connect();
            $query = $db->table('areas a')
                        ->select('a.*, COALESCE(ca.name, ca.username) as created_by_name, COALESCE(ua.name, ua.username) as updated_by_name, COUNT(s.id) as society_count')
                        ->join('admins ca', 'ca.id = a.created_by', 'left')
                        ->join('admins ua', 'ua.id = a.updated_by', 'left')
                        ->join('societies s', 's.area_id = a.id AND s.deleted_at IS NULL', 'left')
                        ->where('a.deleted_at', null)
                        ->groupBy('a.id');
    
            // Apply filters
            if (!empty($filters['area_name'])) {
                $query->like('a.area_name', $filters['area_name']);
            }
            if (!empty($filters['status'])) {
                $query->where('a.status', $filters['status']);
            }
            if (!empty($filters['visible_to_customer'])) {
                $query->where('a.visible_to_customer', $filters['visible_to_customer']);
            }
            if (!empty($filters['created_from'])) {
                $query->where('a.created_date >=', $filters['created_from'] . ' 00:00:00');
            }
            if (!empty($filters['created_to'])) {
                $query->where('a.created_date <=', $filters['created_to'] . ' 23:59:59');
            }
    
            $areas = $query->orderBy('a.id', 'DESC')->get()->getResultArray();
    
            // Set headers for CSV download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="areas_' . date('Y-m-d_H-i-s') . '.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');
    
            // Open output stream
            $output = fopen('php://output', 'w');
    
            // Write CSV headers
            fputcsv($output, [
                'ID',
                'Area Name',
                'Status',
                'Visible to Customer',
                'Societies Count',
                'Created Date',
                'Created By',
                'Last Updated',
                'Updated By'
            ]);
    
            // Write data rows
            foreach ($areas as $area) {
                fputcsv($output, [
                    $area['id'],
                    $area['area_name'],
                    ucfirst($area['status']),
                    ucfirst($area['visible_to_customer']),
                    $area['society_count'] ?? 0,
                    $area['created_date'] ? date('d M Y H:i', strtotime($area['created_date'])) : 'N/A',
                    $area['created_by_name'] ?? 'Unknown',
                    $area['updated_at'] ? date('d M Y H:i', strtotime($area['updated_at'])) : 'Never updated',
                    $area['updated_by_name'] ?? 'N/A'
                ]);
            }
    
            fclose($output);
            exit();
        }
}