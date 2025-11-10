<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\RateModel;
use App\Models\SettingsModel;

class Rates extends Controller
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
        return null;
    }

    public function index()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new RateModel();
        $settingsModel = class_exists(SettingsModel::class) ? new SettingsModel() : null;
        $site_logo = $settingsModel ? $settingsModel->getSetting('site_logo') : null;

        $rates = $model->orderBy('id','DESC')->findAll();

        return view('admin/rates/index', [
            'rates' => $rates,
            'site_logo' => $site_logo,
        ]);
    }

    public function create()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $settingsModel = class_exists(SettingsModel::class) ? new SettingsModel() : null;
        $site_logo = $settingsModel ? $settingsModel->getSetting('site_logo') : null;

        return view('admin/rates/create', [
            'site_logo' => $site_logo,
        ]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'basic_rate'     => ['label' => 'Basic Rate', 'rules' => 'required|decimal'],
            'rate'           => ['label' => 'Rate', 'rules' => 'required|decimal'],
            'effective_date' => ['label' => 'Effective Date', 'rules' => 'required|valid_date[Y-m-d]'],
            'status'         => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new RateModel();
        $data = [
            'basic_rate'     => $this->request->getPost('basic_rate'),
            'rate'           => $this->request->getPost('rate'),
            'effective_date' => $this->request->getPost('effective_date'),
            'status'         => $this->request->getPost('status'),
        ];

        if ($model->insert($data)) {
            return redirect()->to(base_url('admin/rates'))->with('success', 'Rate created successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create rate');
    }

    public function edit($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new RateModel();
        $rate = $model->find($id);
        if (!$rate) {
            return redirect()->to(base_url('admin/rates'))->with('error', 'Rate not found');
        }

        $settingsModel = class_exists(SettingsModel::class) ? new SettingsModel() : null;
        $site_logo = $settingsModel ? $settingsModel->getSetting('site_logo') : null;

        return view('admin/rates/edit', [
            'rate' => $rate,
            'site_logo' => $site_logo,
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'basic_rate'     => ['label' => 'Basic Rate', 'rules' => 'required|decimal'],
            'rate'           => ['label' => 'Rate', 'rules' => 'required|decimal'],
            'effective_date' => ['label' => 'Effective Date', 'rules' => 'required|valid_date[Y-m-d]'],
            'status'         => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new RateModel();
        $data = [
            'basic_rate'     => $this->request->getPost('basic_rate'),
            'rate'           => $this->request->getPost('rate'),
            'effective_date' => $this->request->getPost('effective_date'),
            'status'         => $this->request->getPost('status'),
        ];

        if ($model->update($id, $data)) {
            return redirect()->to(base_url('admin/rates'))->with('success', 'Rate updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update rate');
    }

    public function delete($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new RateModel();
        if ($model->delete($id)) {
            return redirect()->to(base_url('admin/rates'))->with('success', 'Rate deleted successfully');
        }
        return redirect()->to(base_url('admin/rates'))->with('error', 'Failed to delete rate');
    }
}