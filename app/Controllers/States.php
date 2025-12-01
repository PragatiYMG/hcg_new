<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\StateModel;
use App\Models\CountryModel;
use App\Models\SettingsModel;

class States extends Controller
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

        $stateModel = new StateModel();
        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        // Fetch states and their country names
        $db = \Config\Database::connect();
        $builder = $db->table('states s')
            ->select('s.*, c.name AS country_name')
            ->join('countries c', 'c.id = s.country_id', 'left')
            ->where('s.deleted_at', null)
            ->orderBy('s.id', 'DESC');
        $states = $builder->get()->getResultArray();

        return view('admin/states/index', [
            'states' => $states,
            'site_logo' => $site_logo,
        ]);
    }

    public function create()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        $countries = (new CountryModel())
            ->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('admin/states/create', [
            'site_logo' => $site_logo,
            'countries' => $countries,
        ]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required|min_length[2]|max_length[150]'],
            'code' => ['label' => 'Code', 'rules' => 'required|max_length[10]|is_unique[states.code]'],
            'country_id' => ['label' => 'Country', 'rules' => 'required|integer|is_not_unique[countries.id]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new StateModel();
        $data = [
            'name' => $this->request->getPost('name'),
            'code' => $this->request->getPost('code'),
            'country_id' => (int)$this->request->getPost('country_id'),
            'status' => $this->request->getPost('status'),
        ];

        if ($model->insert($data)) {
            return redirect()->to(base_url('admin/states'))->with('success', 'State created successfully');
        }
        return redirect()->back()->withInput()->with('error', 'Failed to create state');
    }

    public function edit($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new StateModel();
        $state = $model->find($id);
        if (!$state) {
            return redirect()->to(base_url('admin/states'))->with('error', 'State not found');
        }

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');
        $countries = (new CountryModel())->where('status', 'active')->orderBy('name', 'ASC')->findAll();

        return view('admin/states/edit', [
            'state' => $state,
            'countries' => $countries,
            'site_logo' => $site_logo,
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required|min_length[2]|max_length[150]'],
            'code' => ['label' => 'Code', 'rules' => "required|max_length[10]|is_unique[states.code,id,{$id}]"],
            'country_id' => ['label' => 'Country', 'rules' => 'required|integer|is_not_unique[countries.id]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new StateModel();
        $data = [
            'name' => $this->request->getPost('name'),
            'code' => $this->request->getPost('code'),
            'country_id' => (int)$this->request->getPost('country_id'),
            'status' => $this->request->getPost('status'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($model->update($id, $data)) {
            return redirect()->to(base_url('admin/states'))->with('success', 'State updated successfully');
        }
        return redirect()->back()->withInput()->with('error', 'Failed to update state');
    }

    public function delete($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new StateModel();
        if ($model->delete($id)) {
            return redirect()->to(base_url('admin/states'))->with('success', 'State deleted successfully');
        }
        return redirect()->to(base_url('admin/states'))->with('error', 'Failed to delete state');
    }

    public function getByCountry($countryId)
    {
        $model = new StateModel();
        $state = $model->find($id);
        $states = $model->select('id, name')
            ->where('country_id', $countryId)
            ->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll();
        
        return $this->response->setJSON($states);
    }
}