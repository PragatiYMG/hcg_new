<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CityModel;
use App\Models\CountryModel;
use App\Models\StateModel;
use App\Models\SettingsModel;

class Cities extends Controller
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

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        $db = \Config\Database::connect();
        $builder = $db->table('cities ci')
            ->select('ci.*, c.name AS country_name, s.name AS state_name')
            ->join('countries c', 'c.id = ci.country_id', 'left')
            ->join('states s', 's.id = ci.state_id', 'left')
            ->where('ci.deleted_at', null)
            ->orderBy('ci.id', 'DESC');
        $cities = $builder->get()->getResultArray();

        return view('admin/cities/index', [
            'cities' => $cities,
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
        $states = (new StateModel())
            ->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('admin/cities/create', [
            'site_logo' => $site_logo,
            'countries' => $countries,
            'states' => $states,
        ]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required|min_length[2]|max_length[150]'],
            'code' => ['label' => 'Code', 'rules' => 'required|max_length[10]|is_unique[cities.code]'],
            'country_id' => ['label' => 'Country', 'rules' => 'required|integer|is_not_unique[countries.id]'],
            'state_id' => ['label' => 'State', 'rules' => 'required|integer|is_not_unique[states.id]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new CityModel();
        $data = [
            'name' => $this->request->getPost('name'),
            'code' => $this->request->getPost('code'),
            'country_id' => (int)$this->request->getPost('country_id'),
            'state_id' => (int)$this->request->getPost('state_id'),
            'status' => $this->request->getPost('status'),
        ];

        if ($model->insert($data)) {
            return redirect()->to(base_url('admin/cities'))->with('success', 'City created successfully');
        }
        return redirect()->back()->withInput()->with('error', 'Failed to create city');
    }

    public function edit($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new CityModel();
        $city = $model->find($id);
        if (!$city) {
            return redirect()->to(base_url('admin/cities'))->with('error', 'City not found');
        }

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        $countries = (new CountryModel())->where('status', 'active')->orderBy('name', 'ASC')->findAll();
        $states = (new StateModel())->where('status', 'active')->orderBy('name', 'ASC')->findAll();

        return view('admin/cities/edit', [
            'city' => $city,
            'countries' => $countries,
            'states' => $states,
            'site_logo' => $site_logo,
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => ['label' => 'Name', 'rules' => 'required|min_length[2]|max_length[150]'],
            'code' => ['label' => 'Code', 'rules' => "required|max_length[10]|is_unique[cities.code,id,{$id}]"],
            'country_id' => ['label' => 'Country', 'rules' => 'required|integer|is_not_unique[countries.id]'],
            'state_id' => ['label' => 'State', 'rules' => 'required|integer|is_not_unique[states.id]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new CityModel();
        $data = [
            'name' => $this->request->getPost('name'),
            'code' => $this->request->getPost('code'),
            'country_id' => (int)$this->request->getPost('country_id'),
            'state_id' => (int)$this->request->getPost('state_id'),
            'status' => $this->request->getPost('status'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($model->update($id, $data)) {
            return redirect()->to(base_url('admin/cities'))->with('success', 'City updated successfully');
        }
        return redirect()->back()->withInput()->with('error', 'Failed to update city');
    }

    public function delete($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new CityModel();
        if ($model->delete($id)) {
            return redirect()->to(base_url('admin/cities'))->with('success', 'City deleted successfully');
        }
        return redirect()->to(base_url('admin/cities'))->with('error', 'Failed to delete city');
    }

    public function getByState($stateId)
    {
        $model = new CityModel();
        $cities = $model->select('id, name')
            ->where('state_id', $stateId)
            ->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll();
        
        return $this->response->setJSON($cities);
    }
}