<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CountryModel;
use App\Models\SettingsModel;

class Countries extends Controller
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

        $model = new CountryModel();
        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        $countries = $model->orderBy('id', 'DESC')->findAll();

        return view('admin/countries/index', [
            'countries' => $countries,
            'site_logo' => $site_logo,
        ]);
    }

    public function create()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        return view('admin/countries/create', [
            'site_logo' => $site_logo,
        ]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required|min_length[2]|max_length[150]'],
            'code' => ['label' => 'Code', 'rules' => 'required|max_length[10]|is_unique[countries.code]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new CountryModel();
        $data = [
            'name' => $this->request->getPost('name'),
            'code' => $this->request->getPost('code'),
            'status' => $this->request->getPost('status'),
        ];

        if ($model->insert($data)) {
            return redirect()->to(base_url('admin/countries'))->with('success', 'Country created successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create country');
    }

    public function edit($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new CountryModel();
        $country = $model->find($id);
        if (!$country) {
            return redirect()->to(base_url('admin/countries'))->with('error', 'Country not found');
        }

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        return view('admin/countries/edit', [
            'country' => $country,
            'site_logo' => $site_logo,
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required|min_length[2]|max_length[150]'],
            'code' => ['label' => 'Code', 'rules' => "required|max_length[10]|is_unique[countries.code,id,{$id}]"],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new CountryModel();
        $data = [
            'name' => $this->request->getPost('name'),
            'code' => $this->request->getPost('code'),
            'status' => $this->request->getPost('status'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($model->update($id, $data)) {
            return redirect()->to(base_url('admin/countries'))->with('success', 'Country updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update country');
    }

    public function delete($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new CountryModel();
        if ($model->delete($id)) {
            return redirect()->to(base_url('admin/countries'))->with('success', 'Country deleted successfully');
        }
        return redirect()->to(base_url('admin/countries'))->with('error', 'Failed to delete country');
    }
}