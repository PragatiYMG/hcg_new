<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ChargeModel;
use App\Models\SettingsModel;

class Charges extends Controller
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

        return view('admin/charges/index', ['site_logo' => $site_logo]);
    }

    public function getCharges()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new ChargeModel();
        $charges = $model->orderBy('id', 'DESC')->findAll();

        // Add admin names for display
        $db = \Config\Database::connect();
        foreach ($charges as &$charge) {
            $createdAdmin = $db->table('admins')->select('name, username')->where('id', $charge['created_by'])->get()->getRowArray();
            $charge['created_by_name'] = $createdAdmin ? ($createdAdmin['name'] ?? $createdAdmin['username']) : 'Unknown';

            if (!empty($charge['updated_by'])) {
                $updatedAdmin = $db->table('admins')->select('name, username')->where('id', $charge['updated_by'])->get()->getRowArray();
                $charge['updated_by_name'] = $updatedAdmin ? ($updatedAdmin['name'] ?? $updatedAdmin['username']) : 'Unknown';
            }
        }

        return $this->response->setJSON(['success' => true, 'data' => $charges]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'charge_name'  => ['label' => 'Charge Name', 'rules' => 'required|min_length[2]|max_length[100]'],
            'charge_value' => ['label' => 'Charge Value', 'rules' => 'required|decimal|greater_than_equal_to[0]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $model = new ChargeModel();
        $data = [
            'charge_name'  => $this->request->getPost('charge_name'),
            'charge_value' => $this->request->getPost('charge_value'),
        ];

        if ($model->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Charge created successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to create charge'
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new ChargeModel();
        $charge = $model->find($id);

        if (!$charge) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Charge not found'
            ]);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'charge_name'  => ['label' => 'Charge Name', 'rules' => 'required|min_length[2]|max_length[100]'],
            'charge_value' => ['label' => 'Charge Value', 'rules' => 'required|decimal|greater_than_equal_to[0]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $newChargeName = $this->request->getPost('charge_name');
        $newChargeValue = $this->request->getPost('charge_value');

        // Check if charge value is being changed and show caution
        $cautionMessage = '';
        if ($charge['charge_value'] != $newChargeValue) {
            $cautionMessage = "⚠️ CAUTION: Charge value changed from ₹{$charge['charge_value']} to ₹{$newChargeValue}. This will affect billing calculations.";
        }

        $data = [
            'charge_name'  => $newChargeName,
            'charge_value' => $newChargeValue,
            'updated_date' => date('Y-m-d H:i:s'),
            'updated_by'   => session()->get('admin_id')
        ];

        if ($model->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Charge updated successfully' . ($cautionMessage ? '. ' . $cautionMessage : ''),
                'caution' => $cautionMessage
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update charge'
        ]);
    }
}