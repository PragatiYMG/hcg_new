<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\BillModel;
use App\Models\SettingsModel;

class Bills extends Controller
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

    public function edit($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new BillModel();
        $bill = $model->find($id);
        if (!$bill) {
            return redirect()->to(base_url('admin/bills'))->with('error', 'Bill not found');
        }

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        return view('admin/bills/edit', [
            'bill' => $bill,
            'site_logo' => $site_logo,
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required|min_length[2]|max_length[200]'],
            'tag_line' => ['label' => 'Tag line', 'rules' => 'permit_empty|max_length[255]'],
            'active' => ['label' => 'Active', 'rules' => 'required|in_list[0,1]'],
            'address' => ['label' => 'Address', 'rules' => 'permit_empty'],
            'phone' => ['label' => 'Phone', 'rules' => 'permit_empty|max_length[50]'],
            'emergency_no' => ['label' => 'Emergency No.', 'rules' => 'permit_empty|max_length[50]'],
            'email' => ['label' => 'Email', 'rules' => 'permit_empty|valid_email|max_length[191]'],
            'tin' => ['label' => 'Tin', 'rules' => 'permit_empty|max_length[100]'],
            'website' => ['label' => 'Website', 'rules' => 'permit_empty|valid_url_strict|max_length[255]'],
            'summary_description' => ['label' => 'Summary Description', 'rules' => 'permit_empty'],
            'footer_description' => ['label' => 'Footer Description', 'rules' => 'permit_empty'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new BillModel();
        $data = [
            'name' => $this->request->getPost('name'),
            'tag_line' => $this->request->getPost('tag_line'),
            'active' => (int)$this->request->getPost('active'),
            'address' => $this->request->getPost('address'),
            'phone' => $this->request->getPost('phone'),
            'emergency_no' => $this->request->getPost('emergency_no'),
            'email' => $this->request->getPost('email'),
            'tin' => $this->request->getPost('tin'),
            'website' => $this->request->getPost('website'),
            'summary_description' => $this->request->getPost('summary_description'),
            'footer_description' => $this->request->getPost('footer_description'),
        ];

        if ($model->update($id, $data)) {
            return redirect()->to(base_url('admin/bills/edit/'.$id))->with('success', 'Bill updated successfully');
        }
        return redirect()->back()->withInput()->with('error', 'Failed to update bill');
    }
}