<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\SettingsModel;

class Users extends Controller
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

        return view('admin/users/create', [
            'site_logo' => $site_logo,
        ]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'email' => ['label' => 'Email', 'rules' => 'required|valid_email|is_unique[users.email]'],
            'username' => ['label' => 'Username', 'rules' => 'required|min_length[3]|max_length[100]|is_unique[users.username]'],
            'password' => ['label' => 'Password', 'rules' => 'required|min_length[6]'],
            'active' => ['label' => 'Active', 'rules' => 'required|in_list[0,1]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new UserModel();
        $data = [
            'email' => $this->request->getPost('email'),
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'), // hashed in model
            'active' => (int)$this->request->getPost('active'),
            'status' => $this->request->getPost('status'),
            'last_active' => null,
        ];

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

        return view('admin/users/edit', [
            'user' => $user,
            'site_logo' => $site_logo,
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        // Uniqueness with ignore current record
        $validation->setRules([
            'email' => ['label' => 'Email', 'rules' => "required|valid_email|is_unique[users.email,id,{$id}]"],
            'username' => ['label' => 'Username', 'rules' => "required|min_length[3]|max_length[100]|is_unique[users.username,id,{$id}]"],
            'password' => ['label' => 'Password', 'rules' => 'permit_empty|min_length[6]'],
            'active' => ['label' => 'Active', 'rules' => 'required|in_list[0,1]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new UserModel();
        $data = [
            'email' => $this->request->getPost('email'),
            'username' => $this->request->getPost('username'),
            'active' => (int)$this->request->getPost('active'),
            'status' => $this->request->getPost('status'),
        ];

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