<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\TaxModel;
use App\Models\SettingsModel;

class Taxes extends Controller
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
        $model = new TaxModel();
        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');
        $taxes = $model->orderBy('id', 'DESC')->findAll();
        return view('admin/taxes/index', [
            'taxes' => $taxes,
            'site_logo' => $site_logo,
        ]);
    }

    public function create()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;
        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');
        return view('admin/taxes/create', [
            'site_logo' => $site_logo,
        ]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;
        $validation = \Config\Services::validation();
        $validation->setRules([
            'tax_type' => [
                'label' => 'Tax Type',
                'rules' => 'required|min_length[2]|max_length[100]'
            ],
            'tax_percentage' => [
                'label' => 'Tax Percentage',
                'rules' => 'required|decimal|greater_than_equal_to[0]'
            ],
            'status' => [
                'label' => 'Status',
                'rules' => 'required|in_list[active,inactive]'
            ],
        ]);
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        $model = new TaxModel();
        $data = [
            'tax_type' => $this->request->getPost('tax_type'),
            'tax_percentage' => $this->request->getPost('tax_percentage'),
            'tax_description' => $this->request->getPost('tax_description'),
            'status' => $this->request->getPost('status'),
        ];
        if ($model->insert($data)) {
            return redirect()->to(base_url('admin/taxes'))->with('success', 'Tax created successfully');
        }
        return redirect()->back()->withInput()->with('error', 'Failed to create tax');
    }

    public function edit($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;
        $model = new TaxModel();
        $tax = $model->find($id);
        if (!$tax) {
            return redirect()->to(base_url('admin/taxes'))->with('error', 'Tax not found');
        }
        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');
        return view('admin/taxes/edit', [
            'tax' => $tax,
            'site_logo' => $site_logo,
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;
        $validation = \Config\Services::validation();
        $validation->setRules([
            'tax_type' => [
                'label' => 'Tax Type',
                'rules' => 'required|min_length[2]|max_length[100]'
            ],
            'tax_percentage' => [
                'label' => 'Tax Percentage',
                'rules' => 'required|decimal|greater_than_equal_to[0]'
            ],
            'status' => [
                'label' => 'Status',
                'rules' => 'required|in_list[active,inactive]'
            ],
        ]);
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        $model = new TaxModel();
        $data = [
            'tax_type' => $this->request->getPost('tax_type'),
            'tax_percentage' => $this->request->getPost('tax_percentage'),
            'tax_description' => $this->request->getPost('tax_description'),
            'status' => $this->request->getPost('status'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($model->update($id, $data)) {
            return redirect()->to(base_url('admin/taxes'))->with('success', 'Tax updated successfully');
        }
        return redirect()->back()->withInput()->with('error', 'Failed to update tax');
    }

    public function delete($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;
        $model = new TaxModel();
        if ($model->delete($id)) {
            return redirect()->to(base_url('admin/taxes'))->with('success', 'Tax deleted successfully');
        }
        return redirect()->to(base_url('admin/taxes'))->with('error', 'Failed to delete tax');
    }
}
