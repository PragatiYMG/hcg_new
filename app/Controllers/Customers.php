<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Libraries\ActivityLogger;
use CodeIgniter\HTTP\ResponseInterface;

class Customers extends BaseController
{
    protected $customerModel;
    protected $activityLogger;

    public function __construct()
    {
        helper('url');
        $this->customerModel = new CustomerModel();
        $this->activityLogger = new ActivityLogger();
    }

    public function index()
    {
        // Check permission
        if (!hasPermission('customers.view')) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Access denied');
        }

        return view('admin/customers/index');
    }

    public function getData()
    {
        // Check permission
        if (!hasPermission('customers.view')) {
            return $this->response->setJSON(['error' => 'Access denied'], 403);
        }

        $db = \Config\Database::connect();

        // DataTables parameters
        $draw = $this->request->getPost('draw');
        $start = $this->request->getPost('start') ?? 0;
        $length = $this->request->getPost('length') ?? 10;
        $search = $this->request->getPost('search')['value'] ?? '';
        $orderColumn = $this->request->getPost('order')[0]['column'] ?? 0;
        $orderDir = $this->request->getPost('order')[0]['dir'] ?? 'desc';

        // Column mapping for ordering
        $columns = [
            0 => 'c.id',
            1 => 'c.first_name',
            2 => 'c.last_name',
            3 => 'c.email',
            4 => 'c.primary_mobile',
            5 => 'c.status',
            6 => 'c.created_at'
        ];

        $orderBy = $columns[$orderColumn] ?? 'c.id';

        // Base query
        $query = $db->table('customers c')
                   ->select('c.id, c.first_name, c.middle_name, c.last_name, c.email, c.primary_mobile, c.status, c.created_at, c.updated_at');

        // Apply global search
        if (!empty($search)) {
            $query->groupStart()
                  ->like('c.first_name', $search)
                  ->orLike('c.middle_name', $search)
                  ->orLike('c.last_name', $search)
                  ->orLike('c.email', $search)
                  ->orLike('c.primary_mobile', $search)
                  ->orLike('c.alternate_mobile', $search)
                  ->orLike('c.aadhaar_number', $search)
                  ->groupEnd();
        }

        // Get total records count
        $totalRecords = $query->countAllResults(false);

        // Apply ordering and pagination
        $query->orderBy($orderBy, $orderDir)
              ->limit($length, $start);

        $customers = $query->get()->getResultArray();

        // Format data for DataTables
        $data = [];
        foreach ($customers as $customer) {
            $fullName = trim($customer['first_name'] . ' ' . ($customer['middle_name'] ?: '') . ' ' . $customer['last_name']);

            $statusBadge = '';
            switch($customer['status']) {
                case 'active':
                    $statusBadge = '<span class="badge badge-success">Active</span>';
                    break;
                case 'inactive':
                    $statusBadge = '<span class="badge badge-secondary">Inactive</span>';
                    break;
                case 'pending':
                    $statusBadge = '<span class="badge badge-warning">Pending</span>';
                    break;
                default:
                    $statusBadge = '<span class="badge badge-light">' . ucfirst($customer['status']) . '</span>';
                    break;
            }

            $actions = '';
            if (hasPermission('customers.view')) {
                $actions .= '<a href="' . base_url('admin/customers/' . $customer['id']) . '" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a> ';
            }
            if (hasPermission('customers.edit')) {
                $actions .= '<a href="' . base_url('admin/customers/edit/' . $customer['id']) . '" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a> ';
            }

            $data[] = [
                $customer['id'],
                htmlspecialchars($fullName),
                htmlspecialchars($customer['email']),
                htmlspecialchars($customer['primary_mobile']),
                $statusBadge,
                date('d M Y H:i', strtotime($customer['created_at'])),
                $actions
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords, // Since we're not doing additional filtering
            'data' => $data
        ]);
    }

    public function create()
    {
        // Check permission
        if (!hasPermission('customers.create')) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Access denied');
        }

        return view('admin/customers/create');
    }

    public function store()
    {
        // Check permission
        if (!hasPermission('customers.create')) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Access denied');
        }

        // Custom validation for date of birth (18+ years old)
        $dateOfBirth = $this->request->getPost('date_of_birth');
        if ($dateOfBirth) {
            try {
                // Try to parse various date formats
                $birthDate = date_create_from_format('Y-m-d', $dateOfBirth); // YYYY-MM-DD
                if (!$birthDate) {
                    $birthDate = date_create_from_format('d-m-Y', $dateOfBirth); // DD-MM-YYYY
                }
                if (!$birthDate) {
                    $birthDate = date_create_from_format('m/d/Y', $dateOfBirth); // MM/DD/YYYY
                }
                if (!$birthDate) {
                    $birthDate = new \DateTime($dateOfBirth); // Let PHP try to parse
                }

                if (!$birthDate) {
                    return redirect()->back()->withInput()->with('errors', ['date_of_birth' => 'Invalid date format. Please use YYYY-MM-DD format.']);
                }

                $today = new \DateTime();
                $age = $today->diff($birthDate)->y;

                if ($age < 18) {
                    return redirect()->back()->withInput()->with('errors', ['date_of_birth' => 'Customer must be at least 18 years old']);
                }

                // Check if birth year is not before 1920
                if ($birthDate->format('Y') < 1920) {
                    return redirect()->back()->withInput()->with('errors', ['date_of_birth' => 'Birth year cannot be before 1920']);
                }

                // Ensure date is in correct format for database
                $dateOfBirth = $birthDate->format('Y-m-d');
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('errors', ['date_of_birth' => 'Invalid date format']);
            }
        }

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'middle_name' => $this->request->getPost('middle_name'),
            'last_name' => $this->request->getPost('last_name'),
            'gender' => $this->request->getPost('gender'),
            'date_of_birth' => $dateOfBirth,
            'father_husband_name' => $this->request->getPost('father_husband_name'),
            'mother_name' => $this->request->getPost('mother_name'),
            'primary_mobile' => $this->request->getPost('primary_mobile'),
            'alternate_mobile' => $this->request->getPost('alternate_mobile'),
            'email' => $this->request->getPost('email'),
            'aadhaar_number' => $this->request->getPost('aadhaar_number'),
            'secondary_id_type' => $this->request->getPost('secondary_id_type'),
            'secondary_id_number' => $this->request->getPost('secondary_id_number'),
            'status' => $this->request->getPost('status') ?: 'pending',
            'created_by' => session()->get('admin_id'),
        ];

        // Handle file uploads with year/month directory structure
        $currentYear = date('Y');
        $currentMonth = date('m');

        $customerPhoto = $this->request->getFile('customer_photo');
        if ($customerPhoto && $customerPhoto->isValid() && !$customerPhoto->hasMoved()) {
            $uploadPath = ROOTPATH . 'public/uploads/customers/' . $currentYear . '/' . $currentMonth . '/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $newPhotoName = 'customer_' . time() . '_' . rand(1000, 9999) . '.' . $customerPhoto->getClientExtension();
            $customerPhoto->move($uploadPath, $newPhotoName);
            $data['customer_photo'] = 'customers/' . $currentYear . '/' . $currentMonth . '/' . $newPhotoName;
        }

        $aadhaarAttachment = $this->request->getFile('aadhaar_attachment');
        if ($aadhaarAttachment && $aadhaarAttachment->isValid() && !$aadhaarAttachment->hasMoved()) {
            $uploadPath = ROOTPATH . 'public/uploads/customers/' . $currentYear . '/' . $currentMonth . '/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $newAadhaarName = 'aadhaar_' . time() . '_' . rand(1000, 9999) . '.' . $aadhaarAttachment->getClientExtension();
            $aadhaarAttachment->move($uploadPath, $newAadhaarName);
            $data['aadhaar_attachment'] = 'customers/' . $currentYear . '/' . $currentMonth . '/' . $newAadhaarName;
        }

        $secondaryIdAttachment = $this->request->getFile('secondary_id_attachment');
        if ($secondaryIdAttachment && $secondaryIdAttachment->isValid() && !$secondaryIdAttachment->hasMoved()) {
            $uploadPath = ROOTPATH . 'public/uploads/customers/' . $currentYear . '/' . $currentMonth . '/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $newSecondaryName = 'secondary_' . time() . '_' . rand(1000, 9999) . '.' . $secondaryIdAttachment->getClientExtension();
            $secondaryIdAttachment->move($uploadPath, $newSecondaryName);
            $data['secondary_id_attachment'] = 'customers/' . $currentYear . '/' . $currentMonth . '/' . $newSecondaryName;
        }

        if ($this->customerModel->insert($data)) {
            $customerId = $this->customerModel->getInsertID();

            // Log activity
            $this->activityLogger->logAdd('customers', $customerId, 'Customer created: ' . $data['first_name'] . ' ' . $data['last_name']);

            // Check if this is an AJAX request
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Customer created successfully',
                    'redirect' => base_url('admin/customers')
                ]);
            }

            return redirect()->to(base_url('admin/customers'))->with('success', 'Customer created successfully');
        } else {
            // Get validation errors if any
            $errors = $this->customerModel->errors();

            // Check if this is an AJAX request
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errors ?: ['general' => 'Failed to create customer. Please check your input and try again.']
                ]);
            }

            if (!empty($errors)) {
                return redirect()->back()->withInput()->with('errors', $errors);
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to create customer. Please check your input and try again.');
            }
        }
    }

    public function show($id)
    {
        // Check permission
        if (!hasPermission('customers.view')) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Access denied');
        }

        // Get customer with creator and updater information
        $db = \Config\Database::connect();
        $customer = $db->table('customers c')
                      ->select('c.*, COALESCE(CONCAT(ca.first_name, " ", ca.last_name), ca.username) as created_by_name, COALESCE(CONCAT(ua.first_name, " ", ua.last_name), ua.username) as updated_by_name')
                      ->join('admins ca', 'ca.id = c.created_by', 'left')
                      ->join('admins ua', 'ua.id = c.updated_by', 'left')
                      ->where('c.id', $id)
                      ->get()
                      ->getRowArray();

        if (!$customer) {
            return redirect()->to(base_url('admin/customers'))->with('error', 'Customer not found');
        }

        return view('admin/customers/show', ['customer' => $customer]);
    }

    public function edit($id)
    {
        // Check permission
        if (!hasPermission('customers.edit')) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Access denied');
        }

        $customer = $this->customerModel->find($id);

        if (!$customer) {
            return redirect()->to(base_url('admin/customers'))->with('error', 'Customer not found');
        }

        return view('admin/customers/edit', ['customer' => $customer]);
    }

    public function update($id)
    {
        // Check permission
        if (!hasPermission('customers.edit')) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Access denied');
        }

        $customer = $this->customerModel->find($id);
        if (!$customer) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Customer not found'], 404);
            }
            return redirect()->to(base_url('admin/customers'))->with('error', 'Customer not found');
        }

        // Set custom validation rules for update (excluding current record)
        $validationRules = [
            'first_name' => 'required|min_length[2]|max_length[100]|alpha_space',
            'middle_name' => 'max_length[100]|alpha_space',
            'last_name' => 'required|min_length[2]|max_length[100]|alpha_space',
            'gender' => 'required|in_list[male,female,other]',
            'date_of_birth' => 'required|valid_date',
            'father_husband_name' => 'required|min_length[2]|max_length[255]|alpha_space',
            'mother_name' => 'max_length[255]|alpha_space',
            'primary_mobile' => 'required|exact_length[10]|numeric|is_unique[customers.primary_mobile,id,' . $id . ']',
            'alternate_mobile' => 'exact_length[10]|numeric|is_unique[customers.alternate_mobile,id,' . $id . ']',
            'email' => 'required|valid_email|max_length[255]|is_unique[customers.email,id,' . $id . ']',
            'aadhaar_number' => 'required|exact_length[12]|numeric|is_unique[customers.aadhaar_number,id,' . $id . ']',
            'secondary_id_type' => 'in_list[voter_id,passport,driving_license]',
            'secondary_id_number' => 'max_length[50]'
        ];

        $this->customerModel->setValidationRules($validationRules);

        // Custom validation for date of birth (18+ years old)
        $dateOfBirth = $this->request->getPost('date_of_birth');
        if ($dateOfBirth) {
            try {
                // Try to parse various date formats
                $birthDate = date_create_from_format('Y-m-d', $dateOfBirth); // YYYY-MM-DD
                if (!$birthDate) {
                    $birthDate = date_create_from_format('d-m-Y', $dateOfBirth); // DD-MM-YYYY
                }
                if (!$birthDate) {
                    $birthDate = date_create_from_format('m/d/Y', $dateOfBirth); // MM/DD/YYYY
                }
                if (!$birthDate) {
                    $birthDate = new \DateTime($dateOfBirth); // Let PHP try to parse
                }

                if (!$birthDate) {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Validation failed',
                            'errors' => ['date_of_birth' => 'Invalid date format. Please use YYYY-MM-DD format.']
                        ]);
                    }
                    return redirect()->back()->withInput()->with('errors', ['date_of_birth' => 'Invalid date format. Please use YYYY-MM-DD format.']);
                }

                $today = new \DateTime();
                $age = $today->diff($birthDate)->y;

                if ($age < 18) {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Validation failed',
                            'errors' => ['date_of_birth' => 'Customer must be at least 18 years old']
                        ]);
                    }
                    return redirect()->back()->withInput()->with('errors', ['date_of_birth' => 'Customer must be at least 18 years old']);
                }

                // Check if birth year is not before 1920
                if ($birthDate->format('Y') < 1920) {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Validation failed',
                            'errors' => ['date_of_birth' => 'Birth year cannot be before 1920']
                        ]);
                    }
                    return redirect()->back()->withInput()->with('errors', ['date_of_birth' => 'Birth year cannot be before 1920']);
                }

                // Ensure date is in correct format for database
                $dateOfBirth = $birthDate->format('Y-m-d');
            } catch (\Exception $e) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => ['date_of_birth' => 'Invalid date format']
                    ]);
                }
                return redirect()->back()->withInput()->with('errors', ['date_of_birth' => 'Invalid date format']);
            }
        }

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'middle_name' => $this->request->getPost('middle_name'),
            'last_name' => $this->request->getPost('last_name'),
            'gender' => $this->request->getPost('gender'),
            'date_of_birth' => $dateOfBirth,
            'father_husband_name' => $this->request->getPost('father_husband_name'),
            'mother_name' => $this->request->getPost('mother_name'),
            'primary_mobile' => $this->request->getPost('primary_mobile'),
            'alternate_mobile' => $this->request->getPost('alternate_mobile'),
            'email' => $this->request->getPost('email'),
            'aadhaar_number' => $this->request->getPost('aadhaar_number'),
            'secondary_id_type' => $this->request->getPost('secondary_id_type'),
            'secondary_id_number' => $this->request->getPost('secondary_id_number'),
            'status' => $this->request->getPost('status'),
            'updated_by' => session()->get('admin_id'),
        ];

        // Handle file uploads with year/month directory structure (only if new files are uploaded)
        $currentYear = date('Y');
        $currentMonth = date('m');

        $customerPhoto = $this->request->getFile('customer_photo');
        if ($customerPhoto && $customerPhoto->isValid() && !$customerPhoto->hasMoved()) {
            // Delete old photo if exists
            if ($customer['customer_photo'] && file_exists(ROOTPATH . 'public/uploads/' . $customer['customer_photo'])) {
                unlink(ROOTPATH . 'public/uploads/' . $customer['customer_photo']);
            }

            $uploadPath = ROOTPATH . 'public/uploads/customers/' . $currentYear . '/' . $currentMonth . '/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $newPhotoName = 'customer_' . time() . '_' . rand(1000, 9999) . '.' . $customerPhoto->getClientExtension();
            $customerPhoto->move($uploadPath, $newPhotoName);
            $data['customer_photo'] = 'customers/' . $currentYear . '/' . $currentMonth . '/' . $newPhotoName;
        }

        $aadhaarAttachment = $this->request->getFile('aadhaar_attachment');
        if ($aadhaarAttachment && $aadhaarAttachment->isValid() && !$aadhaarAttachment->hasMoved()) {
            // Delete old aadhaar attachment if exists
            if ($customer['aadhaar_attachment'] && file_exists(ROOTPATH . 'public/uploads/' . $customer['aadhaar_attachment'])) {
                unlink(ROOTPATH . 'public/uploads/' . $customer['aadhaar_attachment']);
            }

            $uploadPath = ROOTPATH . 'public/uploads/customers/' . $currentYear . '/' . $currentMonth . '/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $newAadhaarName = 'aadhaar_' . time() . '_' . rand(1000, 9999) . '.' . $aadhaarAttachment->getClientExtension();
            $aadhaarAttachment->move($uploadPath, $newAadhaarName);
            $data['aadhaar_attachment'] = 'customers/' . $currentYear . '/' . $currentMonth . '/' . $newAadhaarName;
        }

        $secondaryIdAttachment = $this->request->getFile('secondary_id_attachment');
        if ($secondaryIdAttachment && $secondaryIdAttachment->isValid() && !$secondaryIdAttachment->hasMoved()) {
            // Delete old secondary ID attachment if exists
            if ($customer['secondary_id_attachment'] && file_exists(ROOTPATH . 'public/uploads/' . $customer['secondary_id_attachment'])) {
                unlink(ROOTPATH . 'public/uploads/' . $customer['secondary_id_attachment']);
            }

            $uploadPath = ROOTPATH . 'public/uploads/customers/' . $currentYear . '/' . $currentMonth . '/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $newSecondaryName = 'secondary_' . time() . '_' . rand(1000, 9999) . '.' . $secondaryIdAttachment->getClientExtension();
            $secondaryIdAttachment->move($uploadPath, $newSecondaryName);
            $data['secondary_id_attachment'] = 'customers/' . $currentYear . '/' . $currentMonth . '/' . $newSecondaryName;
        }

        if ($this->customerModel->update($id, $data)) {
            // Log activity
            $this->activityLogger->logEdit('customers', $id, 'Customer updated: ' . $data['first_name'] . ' ' . $data['last_name']);

            // Check if this is an AJAX request
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Customer updated successfully',
                    'redirect' => base_url('admin/customers')
                ]);
            }

            return redirect()->to(base_url('admin/customers'))->with('success', 'Customer updated successfully');
        } else {
            // Get validation errors if any
            $errors = $this->customerModel->errors();

            // Check if this is an AJAX request
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errors ?: ['general' => 'Failed to update customer. Please check your input and try again.']
                ]);
            }

            if (!empty($errors)) {
                return redirect()->back()->withInput()->with('errors', $errors);
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to update customer. Please check your input and try again.');
            }
        }
    }

    public function delete($id)
    {
        // Check permission
        if (!hasPermission('customers.delete')) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Access denied');
        }

        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return redirect()->to(base_url('admin/customers'))->with('error', 'Customer not found');
        }

        // Delete associated files
        $filesToDelete = ['customer_photo', 'aadhaar_attachment', 'secondary_id_attachment'];
        foreach ($filesToDelete as $fileField) {
            if ($customer[$fileField] && file_exists(ROOTPATH . 'public/uploads/' . $customer[$fileField])) {
                unlink(ROOTPATH . 'public/uploads/' . $customer[$fileField]);
            }
        }

        if ($this->customerModel->delete($id)) {
            // Log activity
            $this->activityLogger->log('delete', 'customers', $id, 'Customer deleted: ' . $customer['first_name'] . ' ' . $customer['last_name']);

            return redirect()->to(base_url('admin/customers'))->with('success', 'Customer deleted successfully');
        } else {
            return redirect()->to(base_url('admin/customers'))->with('error', 'Failed to delete customer');
        }
    }

    public function toggleStatus($id)
    {
        // Check permission
        if (!hasPermission('customers.edit')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'], 403);
        }

        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return $this->response->setJSON(['success' => false, 'message' => 'Customer not found'], 404);
        }

        $newStatus = ($customer['status'] === 'active') ? 'inactive' : 'active';

        if ($this->customerModel->update($id, [
            'status' => $newStatus,
            'updated_by' => session()->get('admin_id')
        ])) {
            // Log activity
            $this->activityLogger->logEdit('customers', $id, 'Customer status changed to ' . $newStatus);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Customer status updated successfully',
                'new_status' => $newStatus
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update customer status'], 500);
    }
}
