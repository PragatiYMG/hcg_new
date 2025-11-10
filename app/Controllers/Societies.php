<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\SocietyModel;
use App\Models\SettingsModel;

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

        $societies = $model->orderBy('id','DESC')->findAll();

        return view('admin/societies/index', [
            'societies' => $societies,
            'site_logo' => $site_logo,
        ]);
    }

    public function create()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        return view('admin/societies/create', [
            'site_logo' => $site_logo,
        ]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'society_name' => ['label' => 'Society Name', 'rules' => 'required|min_length[2]|max_length[200]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new SocietyModel();
        $data = [
            'society_name' => $this->request->getPost('society_name'),
            'status' => $this->request->getPost('status'),
        ];

        if ($model->insert($data)) {
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

        return view('admin/societies/edit', [
            'society' => $society,
            'site_logo' => $site_logo,
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'society_name' => ['label' => 'Society Name', 'rules' => 'required|min_length[2]|max_length[200]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new SocietyModel();
        $data = [
            'society_name' => $this->request->getPost('society_name'),
            'status' => $this->request->getPost('status'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($model->update($id, $data)) {
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
}