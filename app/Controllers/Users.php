<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\SettingsModel;
use App\Models\SocietyModel;

class Users extends Controller
{
    public function __construct()
    {
        helper(['url', 'text']);
    }

    protected function saveUpload(string $fieldName, ?string $existingPath = null): ?string
    {
        $file = $this->request->getFile($fieldName);
        if (!$file || !$file->isValid()) {
            return $existingPath;
        }
        $dir = FCPATH . 'uploads/users';
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        $newName = time() . '_' . random_string('alnum', 8) . '.' . $file->getExtension();
        if ($file->move($dir, $newName, true)) {
            return 'uploads/users/' . $newName;
        }
        return $existingPath;
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

        $model = new UserModel();
        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        $users = $model->orderBy('id','DESC')->findAll();

        return view('admin/users/index', [
            'users' => $users,
            'site_logo' => $site_logo,
        ]);
    }

    public function create()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        $societyModel = new SocietyModel();
        $societies = $societyModel->where('status', 'active')->orderBy('society_name', 'ASC')->findAll();

        return view('admin/users/create', [
            'site_logo' => $site_logo,
            'societies' => $societies,
        ]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'email' => ['label' => 'Email', 'rules' => 'required|valid_email|is_unique[users.email]'],
            'password' => ['label' => 'Password', 'rules' => 'required|min_length[6]'],
            'active' => ['label' => 'Active', 'rules' => 'required|in_list[0,1]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
            'first_name' => ['label' => 'First Name', 'rules' => 'permit_empty|max_length[100]'],
            'middle_name' => ['label' => 'Middle Name', 'rules' => 'permit_empty|max_length[100]'],
            'last_name' => ['label' => 'Last Name', 'rules' => 'permit_empty|max_length[100]'],
            'gender' => ['label' => 'Gender', 'rules' => 'permit_empty|in_list[male,female,other,company]'],
            'dob' => ['label' => 'Date of Birth', 'rules' => 'permit_empty|valid_date[Y-m-d]'],
            'father_husband_name' => ['label' => 'Father/Husband Name', 'rules' => 'permit_empty|max_length[150]'],
            'mobile' => ['label' => 'Mobile', 'rules' => 'permit_empty|max_length[20]'],
            'phone_office' => ['label' => 'Telephone Office Number', 'rules' => 'permit_empty|max_length[20]'],
            'phone_residence' => ['label' => 'Telephone Residence Number', 'rules' => 'permit_empty|max_length[20]'],
            'house_no' => ['label' => 'House No', 'rules' => 'permit_empty|max_length[50]'],
            'block_no' => ['label' => 'Block No', 'rules' => 'permit_empty|max_length[50]'],
            'plot_no' => ['label' => 'Plot No', 'rules' => 'permit_empty|max_length[50]'],
            'sector' => ['label' => 'Sector', 'rules' => 'permit_empty|max_length[50]'],
            'street_name' => ['label' => 'Street Name', 'rules' => 'permit_empty|max_length[150]'],
            'landmark' => ['label' => 'Landmark', 'rules' => 'permit_empty|max_length[150]'],
            'pincode' => ['label' => 'Pincode', 'rules' => 'permit_empty|max_length[10]'],
            'city' => ['label' => 'City', 'rules' => 'permit_empty|max_length[100]'],
            'state' => ['label' => 'State', 'rules' => 'permit_empty|max_length[100]'],
            'country' => ['label' => 'Country', 'rules' => 'permit_empty|max_length[100]'],
            'society' => ['label' => 'Society', 'rules' => 'permit_empty|max_length[150]'],
            'payment_mode' => ['label' => 'Payment Mode', 'rules' => 'permit_empty|max_length[50]'],
            'registration_fee' => ['label' => 'Registration Fee', 'rules' => 'permit_empty|decimal'],
            'accommodation_type' => ['label' => 'Type of Accommodation', 'rules' => 'permit_empty|max_length[100]'],
            'aadhaar_no' => ['label' => 'Aadhaar No', 'rules' => 'permit_empty|max_length[20]'],
            'primary_id_type' => ['label' => 'Primary ID Type', 'rules' => 'permit_empty|max_length[100]'],
            'primary_id_no' => ['label' => 'Primary ID No', 'rules' => 'permit_empty|max_length[100]'],
            'secondary_id_type' => ['label' => 'Secondary ID Type', 'rules' => 'permit_empty|max_length[100]'],
            'secondary_id_no' => ['label' => 'Secondary ID No', 'rules' => 'permit_empty|max_length[100]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new UserModel();
        // Generate username in format CS00001 using next id sequence
        $row = $model->selectMax('id')->first();
        $nextId = (int)($row['id'] ?? 0) + 1;
        $generatedUsername = 'CS' . str_pad((string)$nextId, 5, '0', STR_PAD_LEFT);

        $data = [
            'email' => $this->request->getPost('email'),
            'username' => $generatedUsername,
            'password' => $this->request->getPost('password'), // hashed in model
            'active' => (int)$this->request->getPost('active'),
            'status' => $this->request->getPost('status'),
            'last_active' => null,
            'first_name' => $this->request->getPost('first_name'),
            'middle_name' => $this->request->getPost('middle_name'),
            'last_name' => $this->request->getPost('last_name'),
            'gender' => $this->request->getPost('gender'),
            'dob' => $this->request->getPost('dob') ?: null,
            'father_husband_name' => $this->request->getPost('father_husband_name'),
            'mobile' => $this->request->getPost('mobile'),
            'phone_office' => $this->request->getPost('phone_office'),
            'phone_residence' => $this->request->getPost('phone_residence'),
            'house_no' => $this->request->getPost('house_no'),
            'block_no' => $this->request->getPost('block_no'),
            'plot_no' => $this->request->getPost('plot_no'),
            'sector' => $this->request->getPost('sector'),
            'street_name' => $this->request->getPost('street_name'),
            'landmark' => $this->request->getPost('landmark'),
            'pincode' => $this->request->getPost('pincode'),
            'city' => $this->request->getPost('city'),
            'state' => $this->request->getPost('state'),
            'country' => $this->request->getPost('country'),
            'society' => $this->request->getPost('society'),
            'payment_mode' => $this->request->getPost('payment_mode'),
            'registration_fee' => $this->request->getPost('registration_fee'),
            'accommodation_type' => $this->request->getPost('accommodation_type'),
            'aadhaar_no' => $this->request->getPost('aadhaar_no'),
        ];

        // file uploads
        $data['photo_path'] = $this->saveUpload('photo_path');
        $data['aadhaar_file_path'] = $this->saveUpload('aadhaar_file_path');
        $data['primary_id_file_path'] = $this->saveUpload('primary_id_file_path');
        $data['secondary_id_file_path'] = $this->saveUpload('secondary_id_file_path');

        // IDs
        $data['primary_id_type'] = $this->request->getPost('primary_id_type');
        $data['primary_id_no'] = $this->request->getPost('primary_id_no');
        $data['secondary_id_type'] = $this->request->getPost('secondary_id_type');
        $data['secondary_id_no'] = $this->request->getPost('secondary_id_no');

        if ($model->insert($data)) {
            return redirect()->to(base_url('admin/users'))->with('success', 'User created successfully');
        }
        return redirect()->back()->withInput()->with('error', 'Failed to create user');
    }

    public function edit($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new UserModel();
        $user = $model->find($id);
        if (!$user) {
            return redirect()->to(base_url('admin/users'))->with('error', 'User not found');
        }

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        $societyModel = new SocietyModel();
        $societies = $societyModel->where('status', 'active')->orderBy('society_name', 'ASC')->findAll();

        return view('admin/users/edit', [
            'user' => $user,
            'site_logo' => $site_logo,
            'societies' => $societies,
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        // Uniqueness with ignore current record
        $validation->setRules([
            'email' => ['label' => 'Email', 'rules' => "required|valid_email|is_unique[users.email,id,{$id}]"],
            'password' => ['label' => 'Password', 'rules' => 'permit_empty|min_length[6]'],
            'active' => ['label' => 'Active', 'rules' => 'required|in_list[0,1]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
            'first_name' => ['label' => 'First Name', 'rules' => 'permit_empty|max_length[100]'],
            'middle_name' => ['label' => 'Middle Name', 'rules' => 'permit_empty|max_length[100]'],
            'last_name' => ['label' => 'Last Name', 'rules' => 'permit_empty|max_length[100]'],
            'gender' => ['label' => 'Gender', 'rules' => 'permit_empty|in_list[male,female,other]'],
            'dob' => ['label' => 'Date of Birth', 'rules' => 'permit_empty|valid_date[Y-m-d]'],
            'father_husband_name' => ['label' => 'Father/Husband Name', 'rules' => 'permit_empty|max_length[150]'],
            'mobile' => ['label' => 'Mobile', 'rules' => 'permit_empty|max_length[20]'],
            'phone_office' => ['label' => 'Telephone Office Number', 'rules' => 'permit_empty|max_length[20]'],
            'phone_residence' => ['label' => 'Telephone Residence Number', 'rules' => 'permit_empty|max_length[20]'],
            'house_no' => ['label' => 'House No', 'rules' => 'permit_empty|max_length[50]'],
            'block_no' => ['label' => 'Block No', 'rules' => 'permit_empty|max_length[50]'],
            'plot_no' => ['label' => 'Plot No', 'rules' => 'permit_empty|max_length[50]'],
            'sector' => ['label' => 'Sector', 'rules' => 'permit_empty|max_length[50]'],
            'street_name' => ['label' => 'Street Name', 'rules' => 'permit_empty|max_length[150]'],
            'landmark' => ['label' => 'Landmark', 'rules' => 'permit_empty|max_length[150]'],
            'pincode' => ['label' => 'Pincode', 'rules' => 'permit_empty|max_length[10]'],
            'city' => ['label' => 'City', 'rules' => 'permit_empty|max_length[100]'],
            'state' => ['label' => 'State', 'rules' => 'permit_empty|max_length[100]'],
            'country' => ['label' => 'Country', 'rules' => 'permit_empty|max_length[100]'],
            'society' => ['label' => 'Society', 'rules' => 'permit_empty|max_length[150]'],
            'payment_mode' => ['label' => 'Payment Mode', 'rules' => 'permit_empty|max_length[50]'],
            'registration_fee' => ['label' => 'Registration Fee', 'rules' => 'permit_empty|decimal'],
            'accommodation_type' => ['label' => 'Type of Accommodation', 'rules' => 'permit_empty|max_length[100]'],
            'aadhaar_no' => ['label' => 'Aadhaar No', 'rules' => 'permit_empty|max_length[20]'],
            'primary_id_type' => ['label' => 'Primary ID Type', 'rules' => 'permit_empty|max_length[100]'],
            'primary_id_no' => ['label' => 'Primary ID No', 'rules' => 'permit_empty|max_length[100]'],
            'secondary_id_type' => ['label' => 'Secondary ID Type', 'rules' => 'permit_empty|max_length[100]'],
            'secondary_id_no' => ['label' => 'Secondary ID No', 'rules' => 'permit_empty|max_length[100]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new UserModel();
        $data = [
            'email' => $this->request->getPost('email'),
            'active' => (int)$this->request->getPost('active'),
            'status' => $this->request->getPost('status'),
            'first_name' => $this->request->getPost('first_name'),
            'middle_name' => $this->request->getPost('middle_name'),
            'last_name' => $this->request->getPost('last_name'),
            'gender' => $this->request->getPost('gender'),
            'dob' => $this->request->getPost('dob') ?: null,
            'father_husband_name' => $this->request->getPost('father_husband_name'),
            'mobile' => $this->request->getPost('mobile'),
            'phone_office' => $this->request->getPost('phone_office'),
            'phone_residence' => $this->request->getPost('phone_residence'),
            'house_no' => $this->request->getPost('house_no'),
            'block_no' => $this->request->getPost('block_no'),
            'plot_no' => $this->request->getPost('plot_no'),
            'sector' => $this->request->getPost('sector'),
            'street_name' => $this->request->getPost('street_name'),
            'landmark' => $this->request->getPost('landmark'),
            'pincode' => $this->request->getPost('pincode'),
            'city' => $this->request->getPost('city'),
            'state' => $this->request->getPost('state'),
            'country' => $this->request->getPost('country'),
            'society' => $this->request->getPost('society'),
            'payment_mode' => $this->request->getPost('payment_mode'),
            'registration_fee' => $this->request->getPost('registration_fee'),
            'accommodation_type' => $this->request->getPost('accommodation_type'),
            'aadhaar_no' => $this->request->getPost('aadhaar_no'),
            'primary_id_type' => $this->request->getPost('primary_id_type'),
            'primary_id_no' => $this->request->getPost('primary_id_no'),
            'secondary_id_type' => $this->request->getPost('secondary_id_type'),
            'secondary_id_no' => $this->request->getPost('secondary_id_no'),
        ];

        // handle upload updates, preserving existing paths
        $data['photo_path'] = $this->saveUpload('photo_path', $user['photo_path'] ?? null);
        $data['aadhaar_file_path'] = $this->saveUpload('aadhaar_file_path', $user['aadhaar_file_path'] ?? null);
        $data['primary_id_file_path'] = $this->saveUpload('primary_id_file_path', $user['primary_id_file_path'] ?? null);
        $data['secondary_id_file_path'] = $this->saveUpload('secondary_id_file_path', $user['secondary_id_file_path'] ?? null);

        $password = $this->request->getPost('password');
        if ($password !== null && $password !== '') {
            $data['password'] = $password; // hashed in model
        }

        if ($model->update($id, $data)) {
            return redirect()->to(base_url('admin/users'))->with('success', 'User updated successfully');
        }
        return redirect()->back()->withInput()->with('error', 'Failed to update user');
    }

    public function delete($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new UserModel();
        if ($model->delete($id)) {
            return redirect()->to(base_url('admin/users'))->with('success', 'User deleted successfully');
        }
        return redirect()->to(base_url('admin/users'))->with('error', 'Failed to delete user');
    }
}