<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\TaxTypeModel;
use App\Models\SettingsModel;

class TaxTypes extends Controller
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

        $model = new TaxTypeModel();
        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        $types = $model->orderBy('id', 'DESC')->findAll();

        return view('admin/taxtypes/index', [
            'types' => $types,
            'site_logo' => $site_logo,
        ]);
    }

    public function create()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        return view('admin/taxtypes/create', [
            'site_logo' => $site_logo,
        ]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'type_name' => ['label' => 'Type Name', 'rules' => 'required|min_length[2]|max_length[150]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
            'online_status' => ['label' => 'Online Status', 'rules' => 'required|in_list[online,offline]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new TaxTypeModel();
        $data = [
            'type_name' => $this->request->getPost('type_name'),
            'status' => $this->request->getPost('status'),
            'online_status' => $this->request->getPost('online_status'),
        ];

        if ($model->insert($data)) {
            return redirect()->to(base_url('admin/tax-types'))->with('success', 'Tax Type created successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create tax type');
    }

    public function edit($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new TaxTypeModel();
        $type = $model->find($id);
        if (!$type) {
            return redirect()->to(base_url('admin/tax-types'))->with('error', 'Tax Type not found');
        }

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        return view('admin/taxtypes/edit', [
            'type' => $type,
            'site_logo' => $site_logo,
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'type_name' => ['label' => 'Type Name', 'rules' => 'required|min_length[2]|max_length[150]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
            'online_status' => ['label' => 'Online Status', 'rules' => 'required|in_list[online,offline]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new TaxTypeModel();
        $data = [
            'type_name' => $this->request->getPost('type_name'),
            'status' => $this->request->getPost('status'),
            'online_status' => $this->request->getPost('online_status'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($model->update($id, $data)) {
            return redirect()->to(base_url('admin/tax-types'))->with('success', 'Tax Type updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update tax type');
    }

    public function delete($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new TaxTypeModel();
        if ($model->delete($id)) {
            return redirect()->to(base_url('admin/tax-types'))->with('success', 'Tax Type deleted successfully');
        }
        return redirect()->to(base_url('admin/tax-types'))->with('error', 'Failed to delete tax type');
    }
}