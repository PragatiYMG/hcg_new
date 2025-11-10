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

        $model = new ChargeModel();
        $settingsModel = class_exists(SettingsModel::class) ? new SettingsModel() : null;
        $site_logo = $settingsModel ? $settingsModel->getSetting('site_logo') : null;

        $charges = $model->orderBy('id','DESC')->findAll();

        return view('admin/charges/index', [
            'charges' => $charges,
            'site_logo' => $site_logo,
        ]);
    }

    public function edit($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new ChargeModel();
        $charge = $model->find($id);
        if (!$charge) {
            return redirect()->to(base_url('admin/charges'))->with('error', 'Charge record not found');
        }

        $settingsModel = class_exists(SettingsModel::class) ? new SettingsModel() : null;
        $site_logo = $settingsModel ? $settingsModel->getSetting('site_logo') : null;

        return view('admin/charges/edit', [
            'charge' => $charge,
            'site_logo' => $site_logo,
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'late_charge'      => ['label' => 'Late Charge', 'rules' => 'required|decimal'],
            'average_charge'   => ['label' => 'Average Charge', 'rules' => 'required|decimal'],
            'bounce_charge'    => ['label' => 'Bounce Charge', 'rules' => 'required|decimal'],
            'no_of_days'       => ['label' => 'No Of Days', 'rules' => 'required|is_natural'],
            'annual_charges'   => ['label' => 'Annual Charges', 'rules' => 'required|decimal'],
            'minimum_charges'  => ['label' => 'Minimum Charges', 'rules' => 'required|decimal'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new ChargeModel();
        $data = [
            'late_charge'     => $this->request->getPost('late_charge'),
            'average_charge'  => $this->request->getPost('average_charge'),
            'bounce_charge'   => $this->request->getPost('bounce_charge'),
            'no_of_days'      => $this->request->getPost('no_of_days'),
            'annual_charges'  => $this->request->getPost('annual_charges'),
            'minimum_charges' => $this->request->getPost('minimum_charges'),
        ];

        if ($model->update($id, $data)) {
            return redirect()->to(base_url('admin/charges'))->with('success', 'Charges updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update charges');
    }
}