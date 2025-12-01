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

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        return view('admin/rates/index', ['site_logo' => $site_logo]);
    }

    public function getRates()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new RateModel();
        $rates = $model->orderBy('id', 'DESC')->findAll();

        // Add admin names for display
        $db = \Config\Database::connect();
        foreach ($rates as &$rate) {
            $createdAdmin = $db->table('admins')->select('name, username')->where('id', $rate['created_by'])->get()->getRowArray();
            $rate['created_by_name'] = $createdAdmin ? ($createdAdmin['name'] ?? $createdAdmin['username']) : 'Unknown';

            if (!empty($rate['updated_by'])) {
                $updatedAdmin = $db->table('admins')->select('name, username')->where('id', $rate['updated_by'])->get()->getRowArray();
                $rate['updated_by_name'] = $updatedAdmin ? ($updatedAdmin['name'] ?? $updatedAdmin['username']) : 'Unknown';
            }
        }

        return $this->response->setJSON(['success' => true, 'data' => $rates]);
    }

    public function getTaxRates()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $db = \Config\Database::connect();
        $taxes = $db->table('tax_types')
            ->select('type_name, tax_rate')
            ->where('status', 'active')
            ->where('online_status', 'online')
            ->get()
            ->getResultArray();

        $taxRates = [];
        foreach ($taxes as $tax) {
            $taxRates[$tax['type_name']] = (float)$tax['tax_rate'];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $taxRates
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
            'basic_rate'     => ['label' => 'Basic Rate', 'rules' => 'required|decimal|greater_than[0]'],
            'full_rate'      => ['label' => 'Full Rate', 'rules' => 'required|decimal|greater_than[0]'],
            'effective_date' => ['label' => 'Effective Date', 'rules' => 'required|valid_date[Y-m-d]'],
            'status'         => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $model = new RateModel();
        $status = $this->request->getPost('status');

        // If setting to active, deactivate all other rates
        if ($status === 'active') {
            $model->where('status', 'active')->set(['status' => 'inactive'])->update();
        }

        $data = [
            'basic_rate'     => $this->request->getPost('basic_rate'),
            'full_rate'      => $this->request->getPost('full_rate'),
            'effective_date' => $this->request->getPost('effective_date'),
            'status'         => $status,
        ];

        if ($model->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Rate created successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to create rate'
        ]);
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

        $model = new RateModel();
        $rate = $model->find($id);

        if (!$rate) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Rate not found'
            ]);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'basic_rate'     => ['label' => 'Basic Rate', 'rules' => 'required|decimal|greater_than[0]'],
            'full_rate'      => ['label' => 'Full Rate', 'rules' => 'required|decimal|greater_than[0]'],
            'effective_date' => ['label' => 'Effective Date', 'rules' => 'required|valid_date[Y-m-d]'],
            'status'         => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $status = $this->request->getPost('status');
        $newBasicRate = $this->request->getPost('basic_rate');
        $newFullRate = $this->request->getPost('full_rate');

        // If setting to active, deactivate all other rates
        if ($status === 'active') {
            $model->where('status', 'active')->where('id !=', $id)->set(['status' => 'inactive'])->update();
        }

        // Check if rates are being changed and show caution
        $cautionMessage = '';
        if ($rate['basic_rate'] != $newBasicRate || $rate['full_rate'] != $newFullRate) {
            $cautionMessage = "⚠️ CAUTION: Rate changed from Basic: {$rate['basic_rate']}, Full: {$rate['full_rate']} to Basic: {$newBasicRate}, Full: {$newFullRate}. This will affect all billing calculations.";
        }

        $data = [
            'basic_rate'     => $newBasicRate,
            'full_rate'      => $newFullRate,
            'effective_date' => $this->request->getPost('effective_date'),
            'status'         => $status,
            'updated_date'   => date('Y-m-d H:i:s'),
            'updated_by'     => session()->get('admin_id')
        ];

        if ($model->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Rate updated successfully' . ($cautionMessage ? '. ' . $cautionMessage : ''),
                'caution' => $cautionMessage
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update rate'
        ]);
    }

}