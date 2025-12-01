<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ConnectionFeeModel;
use App\Models\SettingsModel;

class ConnectionFees extends Controller
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

        return view('admin/connectionfees/index', [
            'site_logo' => $site_logo,
        ]);
    }

    public function getTableData()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        // Build query with created by username
        $db = \Config\Database::connect();
        $fees = $db->table('connection_fees cf')
                   ->select('cf.*, COALESCE(a.username, a.email) as created_by_name')
                   ->join('admins a', 'a.id = cf.created_by', 'left')
                   ->where('cf.deleted_at', null)
                   ->orderBy('cf.effective_date', 'DESC')
                   ->get()
                   ->getResultArray();

        // Format data for DataTable
        $data = [];
        foreach ($fees as $i => $fee) {
            $statusBadge = '';
            switch ($fee['status']) {
                case 'active':
                    $statusBadge = '<span class="badge badge-success">Active</span>';
                    break;
                case 'inactive':
                    $statusBadge = '<span class="badge badge-secondary">Inactive</span>';
                    break;
            }

            $actions = '<button class="btn btn-sm btn-outline-primary" onclick="editFee(' . $fee['id'] . ')"><i class="fas fa-edit"></i> Edit</button> ';

            $createdInfo = '';
            if ($fee['created_at']) {
                $createdInfo = date('d M Y H:i', strtotime($fee['created_at']));
                if ($fee['created_by_name']) {
                    $createdInfo .= '<br><small class="text-muted">by ' . esc($fee['created_by_name']) . '</small>';
                }
            } else {
                $createdInfo = '-';
            }

            $data[] = [
                'index' => $i + 1,
                'id' => $fee['id'],
                'total_fee' => 'Rs. ' . number_format($fee['total_fee'], 2),
                'refundable_fee' => 'Rs. ' . number_format($fee['refundable_fee'], 2),
                'non_refundable_fee' => 'Rs. ' . number_format($fee['non_refundable_fee'], 2),
                'effective_date' => $fee['effective_date'] ? date('d M Y', strtotime($fee['effective_date'])) : '-',
                'status' => $statusBadge,
                'created_by' => $createdInfo,
                'actions' => $actions
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($this->request->getGet('draw')),
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
            'data' => $data
        ]);
    }

    public function create()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        return view('admin/connectionfees/create', [
            'site_logo' => $site_logo,
        ]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'total_fee' => ['label' => 'Total Fee', 'rules' => 'required|decimal|greater_than[0]'],
            'refundable_fee' => ['label' => 'Refundable Fee', 'rules' => 'required|decimal|greater_than_equal_to[0]'],
            'non_refundable_fee' => ['label' => 'Non-Refundable Fee', 'rules' => 'required|decimal|greater_than_equal_to[0]'],
            'effective_date' => ['label' => 'Effective Date', 'rules' => 'required|valid_date[Y-m-d]'],
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        // Custom validation: total_fee should equal refundable_fee + non_refundable_fee
        $totalFee = $this->request->getPost('total_fee');
        $refundableFee = $this->request->getPost('refundable_fee');
        $nonRefundableFee = $this->request->getPost('non_refundable_fee');

        if (is_numeric($totalFee) && is_numeric($refundableFee) && is_numeric($nonRefundableFee)) {
            $calculatedTotal = floatval($refundableFee) + floatval($nonRefundableFee);
            if (abs(floatval($totalFee) - $calculatedTotal) > 0.01) { // Allow for small floating point differences
                $validation->setRules([
                    'total_fee' => ['label' => 'Total Fee', 'rules' => 'required|decimal|greater_than[0]', 'errors' => ['required' => 'Total fee must equal refundable + non-refundable fee']]
                ]);
            }
        }

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $model = new ConnectionFeeModel();
        $data = [
            'total_fee' => $this->request->getPost('total_fee'),
            'refundable_fee' => $this->request->getPost('refundable_fee'),
            'non_refundable_fee' => $this->request->getPost('non_refundable_fee'),
            'effective_date' => $this->request->getPost('effective_date'),
            'status' => $this->request->getPost('status'),
        ];

        if ($model->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Connection fee created successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to create connection fee'
        ]);
    }

    public function edit($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new ConnectionFeeModel();
        $fee = $model->find($id);
        if (!$fee) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Connection fee not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $fee
        ]);
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'status' => ['label' => 'Status', 'rules' => 'required|in_list[active,inactive]'],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $model = new ConnectionFeeModel();

        // For updates, only allow status changes (amounts and date are read-only)
        $data = [
            'status' => $this->request->getPost('status'),
        ];

        if ($model->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Connection fee status updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update connection fee status'
        ]);
    }

    public function getActiveFee()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new ConnectionFeeModel();
        $fee = $model->getActiveFee();

        return $this->response->setJSON([
            'success' => true,
            'data' => $fee
        ]);
    }
}
