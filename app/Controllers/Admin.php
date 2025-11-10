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

        $areaModel = new AreaModel();
        $areas = $areaModel->findAll();

        return view('admin/areas/index', ['areas' => $areas]);
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
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'area_name' => $this->request->getPost('area_name'),
            'status' => $this->request->getPost('status')
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
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'area_name' => $this->request->getPost('area_name'),
            'status' => $this->request->getPost('status')
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
}