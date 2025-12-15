<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\SettingsModel;
use App\Models\AreaModel;
use App\Libraries\ActivityLogger;
use CodeIgniter\Controller;

class Admin extends Controller
{
    public function __construct()
    {
        helper('url');
    }

    public function index()
    {
        if (session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/dashboard'));
        } else {
            return redirect()->to(base_url('admin/login'));
        }
    }

    public function login()
    {
        if (session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/dashboard'));
        }

        return view('admin/login');
    }

    public function authenticate()
    {
        $model = new AdminModel();

        $identifier = $this->request->getPost('email'); // Can be email or username
        $password = $this->request->getPost('password');

        // Try to find by email first
        $admin = $model->where('email', $identifier)->first();

        // If not found by email, try by username
        if (!$admin) {
            $admin = $model->where('username', $identifier)->first();
        }

        if ($admin && password_verify($password, $admin['password'])) {
            // Check if admin account is active
            if ($admin['active'] != 1) {
                session()->setFlashdata('error', 'Your account has been deactivated. Please contact administrator.');
                return redirect()->back()->withInput();
            }

            session()->set([
                'admin_id' => $admin['id'],
                'admin_username' => $admin['username'],
                'admin_role' => $admin['role'],
                'admin_logged_in' => true
            ]);

            // Log login activity
            $activityLogger = new ActivityLogger();
            $activityLogger->logLogin($admin['id'], 'Admin logged in');

            // Check if password reset is forced
            if ($admin['force_password_reset']) {
                session()->setFlashdata('warning', 'Your password has expired. Please change your password to continue.');
                return redirect()->to(base_url('admin/profile'));
            }

            // Check if there's a redirect URL stored
            $redirectUrl = session()->get('admin_redirect_url');
            if ($redirectUrl) {
                session()->remove('admin_redirect_url');
                return redirect()->to($redirectUrl);
            }

            return redirect()->to(base_url('admin/dashboard'));
        } else {
            session()->setFlashdata('error', 'Invalid username/email or password');
            return redirect()->back()->withInput();
        }
    }

    public function dashboard()
    {
        // Check permission
        if (!hasPermission('dashboard.view')) {
            return redirect()->to(base_url('admin/login'))->with('error', 'Access denied');
        }

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        // Get admin data for greeting
        $adminModel = new AdminModel();
        $admin = $adminModel->find(session()->get('admin_id'));

        // Get metrics data
        $areaModel = new AreaModel();
        $societyModel = new \App\Models\SocietyModel();
        $customerModel = new \App\Models\CustomerModel();
        $billModel = new \App\Models\BillModel();

        $metrics = [
            'total_admins' => $adminModel->countAllResults(),
            'total_areas' => $areaModel->countAllResults(),
            'total_societies' => $societyModel->countAllResults(),
            'total_customers' => $customerModel->countAllResults(),
            'active_customers' => $customerModel->where('status', 'active')->countAllResults(),
            'pending_customers' => $customerModel->where('status', 'pending')->countAllResults(),
            'total_bills' => $billModel->countAllResults()
        ];

        return view('admin/dashboard', ['site_logo' => $site_logo, 'metrics' => $metrics, 'admin' => $admin]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('admin/login'));
    }

    public function profile()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        // Check permission
        if (!hasPermission('profile.view')) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Access denied');
        }

        $adminModel = new AdminModel();
        $admin = $adminModel->find(session()->get('admin_id'));

        return view('admin/profile', ['admin' => $admin]);
    }

    public function updateProfile()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        $adminModel = new AdminModel();
        $adminId = session()->get('admin_id');

        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => [
                'label' => 'Username',
                'rules' => 'required|min_length[3]|max_length[100]|is_unique[admins.username,id,' . $adminId . ']',
                'errors' => [
                    'required' => 'Username is required',
                    'min_length' => 'Username must be at least 3 characters',
                    'max_length' => 'Username cannot exceed 100 characters',
                    'is_unique' => 'This username is already taken'
                ]
            ],
            'first_name' => [
                'label' => 'First Name',
                'rules' => 'required|min_length[2]|max_length[100]',
                'errors' => [
                    'required' => 'First name is required',
                    'min_length' => 'First name must be at least 2 characters',
                    'max_length' => 'First name cannot exceed 100 characters'
                ]
            ],
            'last_name' => [
                'label' => 'Last Name',
                'rules' => 'required|min_length[2]|max_length[100]',
                'errors' => [
                    'required' => 'Last name is required',
                    'min_length' => 'Last name must be at least 2 characters',
                    'max_length' => 'Last name cannot exceed 100 characters'
                ]
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|max_length[100]|is_unique[admins.email,id,' . $adminId . ']',
                'errors' => [
                    'required' => 'Email is required',
                    'valid_email' => 'Please enter a valid email address',
                    'max_length' => 'Email cannot exceed 100 characters',
                    'is_unique' => 'This email is already registered'
                ]
            ],
            'profile_picture' => [
                'label' => 'Profile Picture',
                'rules' => 'max_size[profile_picture,2048]|is_image[profile_picture]|mime_in[profile_picture,image/jpg,image/jpeg,image/png,image/webp]',
                'errors' => [
                    'max_size' => 'Profile picture size should not exceed 2MB',
                    'is_image' => 'Please upload a valid image file',
                    'mime_in' => 'Only JPG, JPEG, PNG and WebP images are allowed'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email')
        ];

        // Handle profile picture upload
        $profilePicture = $this->request->getFile('profile_picture');
        if ($profilePicture->isValid() && !$profilePicture->hasMoved()) {
            // Delete old profile picture if exists
            $oldAdmin = $adminModel->find($adminId);
            if ($oldAdmin && $oldAdmin['profile_picture'] && file_exists(ROOTPATH . 'public/uploads/Admin_Profile/' . $oldAdmin['profile_picture'])) {
                unlink(ROOTPATH . 'public/uploads/Admin_Profile/' . $oldAdmin['profile_picture']);
            }

            $uploadPath = ROOTPATH . 'public/uploads/Admin_Profile/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $newPictureName = 'profile_' . $adminId . '_' . time() . '.' . $profilePicture->getClientExtension();
            $profilePicture->move($uploadPath, $newPictureName);
            $data['profile_picture'] = $newPictureName;

            // Update session with new profile picture
            session()->set('admin_profile_picture', $newPictureName);
        }

        if ($adminModel->update($adminId, $data)) {
            // Update session data
            session()->set([
                'admin_username' => $data['username'],
                'admin_name' => $data['first_name'] . ' ' . $data['last_name'],
                'admin_email' => $data['email']
            ]);

            return redirect()->back()->with('success', 'Profile updated successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update profile');
        }
    }

    public function changePassword()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'current_password' => [
                'label' => 'Current Password',
                'rules' => 'required',
                'errors' => [
                    'required' => 'Current password is required'
                ]
            ],
            'new_password' => [
                'label' => 'New Password',
                'rules' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
                'errors' => [
                    'required' => 'New password is required',
                    'min_length' => 'Password must be at least 8 characters long',
                    'regex_match' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character'
                ]
            ],
            'confirm_password' => [
                'label' => 'Confirm Password',
                'rules' => 'required|matches[new_password]',
                'errors' => [
                    'required' => 'Please confirm your new password',
                    'matches' => 'Passwords do not match'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('password_errors', $validation->getErrors());
        }

        $adminModel = new AdminModel();
        $adminId = session()->get('admin_id');
        $admin = $adminModel->find($adminId);

        // Verify current password
        if (!password_verify($this->request->getPost('current_password'), $admin['password'])) {
            return redirect()->back()->withInput()->with('password_errors', ['current_password' => 'Current password is incorrect']);
        }

        // Update password and reset force_password_reset flag
        $newPassword = password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT);
        $updateData = ['password' => $newPassword];

        // Reset force password reset flag if it was set
        if ($admin['force_password_reset']) {
            $updateData['force_password_reset'] = 0;
        }

        if ($adminModel->update($adminId, $updateData)) {
            return redirect()->back()->with('password_success', 'Password changed successfully');
        } else {
            return redirect()->back()->withInput()->with('password_errors', ['general' => 'Failed to change password']);
        }
    }

    public function adminUsers()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        // Check permission
        if (!hasPermission('admin_users.view')) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Access denied');
        }

        $departments = (new \App\Models\DepartmentModel())->where('status', 'active')->orderBy('department_name', 'ASC')->findAll();

        return view('admin/admin_users/index', ['departments' => $departments]);
    }

    public function getAdminUsersData()
    {
        try {
            if (!session()->get('admin_logged_in')) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            // Check permission
            if (!hasPermission('admin_users.view')) {
                return $this->response->setJSON(['success' => false, 'message' => 'Access denied'], 403);
            }

            $adminModel = new AdminModel();

            // Get filter parameters
            $filters = [
                'username' => $this->request->getGet('username'),
                'first_name' => $this->request->getGet('first_name'),
                'last_name' => $this->request->getGet('last_name'),
                'department_id' => $this->request->getGet('department_id'),
                'mobile' => $this->request->getGet('mobile'),
                'email' => $this->request->getGet('email'),
                'active' => $this->request->getGet('active'),
                'sms_2fa_enabled' => $this->request->getGet('sms_2fa_enabled'),
            ];

            // Build query with filters and department join (exclude password for security)
            $db = \Config\Database::connect();
            $query = $db->table('admins a')
                        ->select('a.id, a.username, a.email, a.first_name, a.last_name, a.department_id, a.mobile, a.role, a.force_password_reset, a.sms_2fa_enabled, a.active, a.profile_picture, a.created_at, a.updated_at, d.department_name')
                        ->join('departments d', 'd.id = a.department_id', 'left');

            // Apply filters
            if (!empty($filters['username'])) {
                $query->like('a.username', $filters['username']);
            }
            if (!empty($filters['first_name'])) {
                $query->like('a.first_name', $filters['first_name']);
            }
            if (!empty($filters['last_name'])) {
                $query->like('a.last_name', $filters['last_name']);
            }
            if (!empty($filters['department_id'])) {
                $query->where('a.department_id', $filters['department_id']);
            }
            if (!empty($filters['mobile'])) {
                $query->like('a.mobile', $filters['mobile']);
            }
            if (!empty($filters['email'])) {
                $query->like('a.email', $filters['email']);
            }
            if ($filters['active'] !== null && $filters['active'] !== '') {
                $query->where('a.active', $filters['active']);
            }
            if ($filters['sms_2fa_enabled'] !== null && $filters['sms_2fa_enabled'] !== '') {
                $query->where('a.sms_2fa_enabled', $filters['sms_2fa_enabled']);
            }

            $admins = $query->orderBy('a.id', 'DESC')->get()->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $admins
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Admin users AJAX error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Server error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createAdminUser()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        // Check permission
        if (!hasPermission('admin_users.create')) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Access denied');
        }

        $departments = (new \App\Models\DepartmentModel())->where('status', 'active')->orderBy('department_name', 'ASC')->findAll();

        return view('admin/admin_users/create', ['departments' => $departments]);
    }

    public function storeAdminUser()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        // Check permission
        if (!hasPermission('admin_users.create')) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Access denied');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => [
                'label' => 'Username',
                'rules' => 'required|min_length[3]|max_length[100]|is_unique[admins.username]',
                'errors' => [
                    'required' => 'Username is required',
                    'min_length' => 'Username must be at least 3 characters',
                    'max_length' => 'Username cannot exceed 100 characters',
                    'is_unique' => 'This username is already taken'
                ]
            ],
            'first_name' => [
                'label' => 'First Name',
                'rules' => 'required|min_length[2]|max_length[100]',
                'errors' => [
                    'required' => 'First name is required',
                    'min_length' => 'First name must be at least 2 characters',
                    'max_length' => 'First name cannot exceed 100 characters'
                ]
            ],
            'last_name' => [
                'label' => 'Last Name',
                'rules' => 'required|min_length[2]|max_length[100]',
                'errors' => [
                    'required' => 'Last name is required',
                    'min_length' => 'Last name must be at least 2 characters',
                    'max_length' => 'Last name cannot exceed 100 characters'
                ]
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|max_length[100]|is_unique[admins.email]',
                'errors' => [
                    'required' => 'Email is required',
                    'valid_email' => 'Please enter a valid email address',
                    'max_length' => 'Email cannot exceed 100 characters',
                    'is_unique' => 'This email is already registered'
                ]
            ],
            'mobile' => [
                'label' => 'Mobile Number',
                'rules' => 'required|max_length[20]|is_unique[admins.mobile]|regex_match[/^[6-9]\d{9}$/]',
                'errors' => [
                    'required' => 'Mobile number is required',
                    'max_length' => 'Mobile number cannot exceed 20 characters',
                    'is_unique' => 'This mobile number is already registered',
                    'regex_match' => 'Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9'
                ]
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
                'errors' => [
                    'required' => 'Password is required',
                    'min_length' => 'Password must be at least 8 characters long',
                    'regex_match' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character'
                ]
            ],
            'confirm_password' => [
                'label' => 'Confirm Password',
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Please confirm the password',
                    'matches' => 'Passwords do not match'
                ]
            ],
            'role' => [
                'label' => 'Role',
                'rules' => 'required|in_list[super_admin,admin,employee]',
                'errors' => [
                    'required' => 'Role is required',
                    'in_list' => 'Role must be either Super Admin, Admin, or Employee'
                ]
            ],
            'profile_picture' => [
                'label' => 'Profile Picture',
                'rules' => 'max_size[profile_picture,2048]|is_image[profile_picture]|mime_in[profile_picture,image/jpg,image/jpeg,image/png,image/webp]',
                'errors' => [
                    'max_size' => 'Profile picture size should not exceed 2MB',
                    'is_image' => 'Please upload a valid image file',
                    'mime_in' => 'Only JPG, JPEG, PNG and WebP images are allowed'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'mobile' => $this->request->getPost('mobile'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $this->request->getPost('role'),
            'department_id' => $this->request->getPost('department_id') ? (int)$this->request->getPost('department_id') : null,
            'active' => (int)$this->request->getPost('active'),
            'force_password_reset' => 0,
            'sms_2fa_enabled' => 0
        ];

        // Handle profile picture upload
        $profilePicture = $this->request->getFile('profile_picture');
        if ($profilePicture->isValid() && !$profilePicture->hasMoved()) {
            $uploadPath = ROOTPATH . 'public/uploads/Admin_Profile/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $newPictureName = 'profile_' . time() . '_' . rand(1000, 9999) . '.' . $profilePicture->getClientExtension();
            $profilePicture->move($uploadPath, $newPictureName);
            $data['profile_picture'] = $newPictureName;
        }

        if ($adminModel->insert($data)) {
            return redirect()->to(base_url('admin/admin-users'))->with('success', 'Admin user created successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create admin user');
        }
    }

    public function editAdminUser($id)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        // Check permission
        if (!hasPermission('admin_users.edit')) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Access denied');
        }

        $adminModel = new AdminModel();
        $admin = $adminModel->find($id);

        if (!$admin) {
            return redirect()->to(base_url('admin/admin-users'))->with('error', 'Admin user not found');
        }

        $departments = (new \App\Models\DepartmentModel())->where('status', 'active')->orderBy('department_name', 'ASC')->findAll();

        return view('admin/admin_users/edit', ['admin' => $admin, 'departments' => $departments]);
    }

    public function toggleAdminStatus($id)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        // Check permission
        if (!hasPermission('admin_users.edit')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ]);
        }

        $adminModel = new AdminModel();
        $admin = $adminModel->find($id);
        if (!$admin) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admin user not found'
            ]);
        }

        // Prevent deactivating own account
        if ($id == session()->get('admin_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You cannot deactivate your own account'
            ]);
        }

        $newStatus = ($admin['active'] ?? 1) == 1 ? 0 : 1;
        $statusText = $newStatus == 1 ? 'activated' : 'deactivated';

        if ($adminModel->update($id, ['active' => $newStatus])) {
            // Log status change activity
            $activityLogger = new ActivityLogger();
            $currentAdmin = $adminModel->find(session()->get('admin_id'));
            $activityLogger->logEdit('admins', $id, 'Admin account ' . $statusText . ' by super admin: ' . $currentAdmin['username']);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Admin account ' . $statusText . ' successfully',
                'new_status' => $newStatus
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update admin status'
        ]);
    }

    public function updateAdminPassword($id)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        // Check permission
        if (!hasPermission('admin_users.edit')) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Access denied');
        }

        $adminModel = new AdminModel();
        $admin = $adminModel->find($id);
        if (!$admin) {
            return redirect()->to(base_url('admin/admin-users'))->with('error', 'Admin user not found');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'new_password' => [
                'label' => 'New Password',
                'rules' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
                'errors' => [
                    'required' => 'New password is required',
                    'min_length' => 'Password must be at least 8 characters long',
                    'regex_match' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character'
                ]
            ],
            'confirm_password' => [
                'label' => 'Confirm Password',
                'rules' => 'required|matches[new_password]',
                'errors' => [
                    'required' => 'Please confirm the password',
                    'matches' => 'Passwords do not match'
                ]
            ],
            'force_password_reset' => [
                'label' => 'Force Password Reset',
                'rules' => 'required|in_list[0,1]',
                'errors' => [
                    'required' => 'Force password reset setting is required',
                    'in_list' => 'Force password reset must be either yes or no'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'password' => password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT),
            'force_password_reset' => (int)$this->request->getPost('force_password_reset')
        ];

        if ($adminModel->update($id, $data)) {
            // Log password change activity
            $activityLogger = new ActivityLogger();
            $currentAdmin = $adminModel->find(session()->get('admin_id'));
            $activityLogger->logEdit('admins', $id, 'Password updated by super admin: ' . $currentAdmin['username']);

            return redirect()->to(base_url('admin/admin-users'))->with('success', 'Admin password updated successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update admin password');
        }
    }

    public function updateAdminUser($id)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        // Check permission
        if (!hasPermission('admin_users.edit')) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Access denied');
        }

        $adminModel = new AdminModel();
        $existingAdmin = $adminModel->find($id);
        if (!$existingAdmin) {
            return redirect()->to(base_url('admin/admin-users'))->with('error', 'Admin user not found');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'first_name' => [
                'label' => 'First Name',
                'rules' => 'required|min_length[2]|max_length[100]',
                'errors' => [
                    'required' => 'First name is required',
                    'min_length' => 'First name must be at least 2 characters',
                    'max_length' => 'First name cannot exceed 100 characters'
                ]
            ],
            'last_name' => [
                'label' => 'Last Name',
                'rules' => 'required|min_length[2]|max_length[100]',
                'errors' => [
                    'required' => 'Last name is required',
                    'min_length' => 'Last name must be at least 2 characters',
                    'max_length' => 'Last name cannot exceed 100 characters'
                ]
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|max_length[100]|is_unique[admins.email,id,' . $id . ']',
                'errors' => [
                    'required' => 'Email is required',
                    'valid_email' => 'Please enter a valid email address',
                    'max_length' => 'Email cannot exceed 100 characters',
                    'is_unique' => 'This email is already registered'
                ]
            ],
            'mobile' => [
                'label' => 'Mobile Number',
                'rules' => 'required|max_length[20]|is_unique[admins.mobile,id,' . $id . ']|regex_match[/^[6-9]\d{9}$/]',
                'errors' => [
                    'required' => 'Mobile number is required',
                    'max_length' => 'Mobile number cannot exceed 20 characters',
                    'is_unique' => 'This mobile number is already registered',
                    'regex_match' => 'Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9'
                ]
            ],
            'role' => [
                'label' => 'Role',
                'rules' => 'required|in_list[super_admin,admin,employee]',
                'errors' => [
                    'required' => 'Role is required',
                    'in_list' => 'Role must be either Super Admin, Admin, or Employee'
                ]
            ],
            'active' => [
                'label' => 'Active Status',
                'rules' => 'required|in_list[0,1]',
                'errors' => [
                    'required' => 'Active status is required',
                    'in_list' => 'Active status must be either Active or Inactive'
                ]
            ],
            'force_password_reset' => [
                'label' => 'Force Password Reset',
                'rules' => 'required|in_list[0,1]',
                'errors' => [
                    'required' => 'Force password reset is required',
                    'in_list' => 'Force password reset must be either yes or no'
                ]
            ],
            'sms_2fa_enabled' => [
                'label' => 'SMS 2FA Enabled',
                'rules' => 'required|in_list[0,1]',
                'errors' => [
                    'required' => 'SMS 2FA setting is required',
                    'in_list' => 'SMS 2FA must be either enabled or disabled'
                ]
            ],
            'active' => [
                'label' => 'Active Status',
                'rules' => 'required|in_list[0,1]',
                'errors' => [
                    'required' => 'Active status is required',
                    'in_list' => 'Active status must be either Active or Inactive'
                ]
            ],
            'profile_picture' => [
                'label' => 'Profile Picture',
                'rules' => 'max_size[profile_picture,2048]|is_image[profile_picture]|mime_in[profile_picture,image/jpg,image/jpeg,image/png,image/webp]',
                'errors' => [
                    'max_size' => 'Profile picture size should not exceed 2MB',
                    'is_image' => 'Please upload a valid image file',
                    'mime_in' => 'Only JPG, JPEG, PNG and WebP images are allowed'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'mobile' => $this->request->getPost('mobile'),
            'role' => $this->request->getPost('role'),
            'department_id' => $this->request->getPost('department_id') ? (int)$this->request->getPost('department_id') : null,
            'active' => (int)$this->request->getPost('active'),
            'force_password_reset' => (int)$this->request->getPost('force_password_reset'),
            'sms_2fa_enabled' => (int)$this->request->getPost('sms_2fa_enabled')
        ];

        // Handle profile picture upload
        $profilePicture = $this->request->getFile('profile_picture');
        if ($profilePicture->isValid() && !$profilePicture->hasMoved()) {
            // Delete old profile picture if exists
            if ($existingAdmin['profile_picture'] && file_exists(ROOTPATH . 'public/uploads/Admin_Profile/' . $existingAdmin['profile_picture'])) {
                unlink(ROOTPATH . 'public/uploads/Admin_Profile/' . $existingAdmin['profile_picture']);
            }

            $uploadPath = ROOTPATH . 'public/uploads/Admin_Profile/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $newPictureName = 'profile_' . time() . '_' . rand(1000, 9999) . '.' . $profilePicture->getClientExtension();
            $profilePicture->move($uploadPath, $newPictureName);
            $data['profile_picture'] = $newPictureName;
        }

        if ($adminModel->update($id, $data)) {
            // If updating own profile, update session data
            if ($id == session()->get('admin_id')) {
                session()->set([
                    'admin_name' => $data['first_name'] . ' ' . $data['last_name'],
                    'admin_role' => $data['role']
                ]);
            }

            return redirect()->to(base_url('admin/admin-users'))->with('success', 'Admin user updated successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update admin user');
        }
    }

    public function settings()
    {
        // Check permission
        if (!hasPermission('settings.view')) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Access denied');
        }

        $settingsModel = new SettingsModel();

        // Get all settings
        $settings = [
            'site_full_name' => $settingsModel->getSetting('site_full_name'),
            'site_short_name' => $settingsModel->getSetting('site_short_name'),
            'site_logo' => $settingsModel->getSetting('site_logo'),
            'site_favicon' => $settingsModel->getSetting('site_favicon'),
            'site_copyright' => $settingsModel->getSetting('site_copyright'),
            'smtp_host' => $settingsModel->getSetting('smtp_host'),
            'smtp_port' => $settingsModel->getSetting('smtp_port'),
            'smtp_username' => $settingsModel->getSetting('smtp_username'),
            'smtp_password' => $settingsModel->getSetting('smtp_password'),
            'smtp_encryption' => $settingsModel->getSetting('smtp_encryption'),
            'from_email' => $settingsModel->getSetting('from_email'),
            'from_name' => $settingsModel->getSetting('from_name'),
        ];

        // Get service location settings
        $serviceLocationJson = $settingsModel->getSetting('service_location');
        $settings['service_location'] = $serviceLocationJson ? json_decode($serviceLocationJson, true) : [];

        // Check if logo and favicon files exist
        $site_logo = $settings['site_logo'];
        $site_favicon = $settings['site_favicon'];

        return view('admin/settings', [
            'settings' => $settings,
            'site_logo' => $site_logo,
            'site_favicon' => $site_favicon
        ]);
    }

    public function updateSettings()
    {

        $settingsModel = new SettingsModel();
        $tab = $this->request->getPost('tab') ?? 'basic';

        $validation = \Config\Services::validation();
        $rules = [];

        // Service Location Settings Validation
        if ($tab === 'service_location') {
            $rules = [
                'state_name' => [
                    'label' => 'State Name',
                    'rules' => 'required|max_length[100]',
                    'errors' => [
                        'required' => 'State name is required',
                        'max_length' => 'State name cannot exceed 100 characters'
                    ]
                ],
                'city_name' => [
                    'label' => 'City Name',
                    'rules' => 'required|max_length[100]',
                    'errors' => [
                        'required' => 'City name is required',
                        'max_length' => 'City name cannot exceed 100 characters'
                    ]
                ],
                'serviceable_pincodes' => [
                    'label' => 'Serviceable Pincodes',
                    'rules' => 'required|max_length[1000]',
                    'errors' => [
                        'required' => 'Serviceable pincodes are required',
                        'max_length' => 'Serviceable pincodes cannot exceed 1000 characters'
                    ]
                ]
            ];
        }

        // Basic Settings Validation
        if ($tab === 'basic') {
            $rules = [
                'site_full_name' => [
                    'label' => 'Full Name',
                    'rules' => 'required|max_length[255]',
                    'errors' => [
                        'required' => 'Site full name is required',
                        'max_length' => 'Full name cannot exceed 255 characters'
                    ]
                ],
                'site_short_name' => [
                    'label' => 'Short Name',
                    'rules' => 'required|max_length[100]',
                    'errors' => [
                        'required' => 'Site short name is required',
                        'max_length' => 'Short name cannot exceed 100 characters'
                    ]
                ],
                'site_copyright' => [
                    'label' => 'Copyright',
                    'rules' => 'required|max_length[255]',
                    'errors' => [
                        'required' => 'Copyright text is required',
                        'max_length' => 'Copyright cannot exceed 255 characters'
                    ]
                ]
            ];

            // Only validate logo if it was uploaded
            if ($this->request->getFile('logo') && $this->request->getFile('logo')->isValid()) {
                $rules['logo'] = [
                    'label' => 'Logo',
                    'rules' => 'max_size[logo,2048]|is_image[logo]|mime_in[logo,image/jpg,image/jpeg,image/png,image/webp]',
                    'errors' => [
                        'max_size' => 'Logo image size should not exceed 2MB',
                        'is_image' => 'Please upload a valid image file',
                        'mime_in' => 'Only JPG, JPEG, PNG and WebP images are allowed'
                    ]
                ];
            }

            // Only validate favicon if it was uploaded
            if ($this->request->getFile('favicon') && $this->request->getFile('favicon')->isValid()) {
                $rules['favicon'] = [
                    'label' => 'Favicon',
                    'rules' => 'max_size[favicon,1024]|is_image[favicon]|mime_in[favicon,image/x-icon,image/vnd.microsoft.icon,image/png]',
                    'errors' => [
                        'max_size' => 'Favicon size should not exceed 1MB',
                        'is_image' => 'Please upload a valid favicon',
                        'mime_in' => 'Only ICO, PNG, or JPG images are allowed for favicon'
                    ]
                ];
            }
        }

        // Email Settings Validation
        if ($tab === 'email') {
            $rules = [
                'smtp_host' => [
                    'label' => 'SMTP Host',
                    'rules' => 'required|max_length[255]',
                    'errors' => [
                        'required' => 'SMTP host is required',
                        'max_length' => 'SMTP host cannot exceed 255 characters'
                    ]
                ],
                'smtp_port' => [
                    'label' => 'SMTP Port',
                    'rules' => 'required|numeric|greater_than[0]|less_than[65536]',
                    'errors' => [
                        'required' => 'SMTP port is required',
                        'numeric' => 'Port must be a number',
                        'greater_than' => 'Port must be greater than 0',
                        'less_than' => 'Port must be less than 65536'
                    ]
                ],
                'smtp_username' => [
                    'label' => 'SMTP Username',
                    'rules' => 'required|max_length[255]',
                    'errors' => [
                        'required' => 'SMTP username is required',
                        'max_length' => 'Username cannot exceed 255 characters'
                    ]
                ],
                'smtp_password' => [
                    'label' => 'SMTP Password',
                    'rules' => 'required|max_length[255]',
                    'errors' => [
                        'required' => 'SMTP password is required',
                        'max_length' => 'Password cannot exceed 255 characters'
                    ]
                ],
                'smtp_encryption' => [
                    'label' => 'SMTP Encryption',
                    'rules' => 'required|in_list[tls,ssl,none]',
                    'errors' => [
                        'required' => 'SMTP encryption is required',
                        'in_list' => 'Encryption must be TLS, SSL, or None'
                    ]
                ],
                'from_email' => [
                    'label' => 'From Email',
                    'rules' => 'required|valid_email|max_length[255]',
                    'errors' => [
                        'required' => 'From email is required',
                        'valid_email' => 'Please enter a valid email address',
                        'max_length' => 'From email cannot exceed 255 characters'
                    ]
                ],
                'from_name' => [
                    'label' => 'From Name',
                    'rules' => 'required|max_length[255]',
                    'errors' => [
                        'required' => 'From name is required',
                        'max_length' => 'From name cannot exceed 255 characters'
                    ]
                ]
            ];
        }

        if (!empty($rules)) {
            $validation->setRules($rules);
            if (!$validation->withRequest($this->request)->run()) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validation->getErrors()
                    ], 400);
                }
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }
        }

        // Update Service Location Settings
        if ($tab === 'service_location') {
            $serviceLocationData = [
                'state_name' => $this->request->getPost('state_name'),
                'city_name' => $this->request->getPost('city_name'),
                'serviceable_pincodes' => $this->request->getPost('serviceable_pincodes')
            ];

            $settingsModel->setSetting('service_location', json_encode($serviceLocationData));

            $message = 'Service location settings updated successfully';
        }

        // Update Basic Settings
        if ($tab === 'basic') {
            $settingsModel->setSetting('site_full_name', $this->request->getPost('site_full_name'));
            $settingsModel->setSetting('site_short_name', $this->request->getPost('site_short_name'));
            $settingsModel->setSetting('site_copyright', $this->request->getPost('site_copyright'));

            // Handle logo upload
            $logo = $this->request->getFile('logo');
            if ($logo && $logo->isValid() && !$logo->hasMoved()) {
                $uploadPath = ROOTPATH . 'public/uploads/basic_settings/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                // Delete old logo if exists
                $oldLogo = $settingsModel->getSetting('site_logo');
                if ($oldLogo && file_exists(ROOTPATH . 'public/uploads/basic_settings/' . $oldLogo)) {
                    unlink(ROOTPATH . 'public/uploads/basic_settings/' . $oldLogo);
                }

                $newLogoName = 'logo_' . time() . '.' . $logo->getClientExtension();
                $logo->move($uploadPath, $newLogoName);
                $settingsModel->setSetting('site_logo', 'basic_settings/' . $newLogoName);
            }

            // Handle favicon upload
            $favicon = $this->request->getFile('favicon');
            if ($favicon && $favicon->isValid() && !$favicon->hasMoved()) {
                $uploadPath = ROOTPATH . 'public/uploads/basic_settings/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                // Delete old favicon if exists
                $oldFavicon = $settingsModel->getSetting('site_favicon');
                if ($oldFavicon && file_exists(ROOTPATH . 'public/uploads/basic_settings/' . basename($oldFavicon))) {
                    unlink(ROOTPATH . 'public/uploads/basic_settings/' . basename($oldFavicon));
                }

                $newFaviconName = 'favicon_' . time() . '.' . ($favicon->getClientExtension() === 'ico' ? 'ico' : 'png');
                $favicon->move($uploadPath, $newFaviconName);
                $settingsModel->setSetting('site_favicon', 'basic_settings/' . $newFaviconName);
            }

            $message = 'Basic settings updated successfully';
        }

        // Update Email Settings
        if ($tab === 'email') {
            $settingsModel->setSetting('smtp_host', $this->request->getPost('smtp_host'));
            $settingsModel->setSetting('smtp_port', $this->request->getPost('smtp_port'));
            $settingsModel->setSetting('smtp_username', $this->request->getPost('smtp_username'));
            $settingsModel->setSetting('smtp_password', $this->request->getPost('smtp_password'));
            $settingsModel->setSetting('smtp_encryption', $this->request->getPost('smtp_encryption'));
            $settingsModel->setSetting('from_email', $this->request->getPost('from_email'));
            $settingsModel->setSetting('from_name', $this->request->getPost('from_name'));

            $message = 'Email settings updated successfully';
        }

        // Check if this is an AJAX request
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $message
            ]);
        }

        return redirect()->to(base_url('admin/settings') . ($tab === 'email' ? '#email' : ($tab === 'service_location' ? '#service-location' : '#basic')))->with('success', $message);
    }

    public function testEmail()
    {
        if (!session()->get('admin_logged_in')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Check permission
        if (!hasPermission('settings.view')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'], 403);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'recipient_email' => [
                'label' => 'Recipient Email',
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'Recipient email is required',
                    'valid_email' => 'Please enter a valid email address'
                ]
            ],
            'message' => [
                'label' => 'Message',
                'rules' => 'required|max_length[1000]',
                'errors' => [
                    'required' => 'Message is required',
                    'max_length' => 'Message cannot exceed 1000 characters'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $validation->getErrors())
            ], 400);
        }

        try {
            $settingsModel = new SettingsModel();

            // Get SMTP settings
            $smtpSettings = [
                'host' => $settingsModel->getSetting('smtp_host'),
                'port' => $settingsModel->getSetting('smtp_port'),
                'username' => $settingsModel->getSetting('smtp_username'),
                'password' => $settingsModel->getSetting('smtp_password'),
                'encryption' => $settingsModel->getSetting('smtp_encryption'),
                'from_email' => $settingsModel->getSetting('from_email'),
                'from_name' => $settingsModel->getSetting('from_name'),
            ];

            // Validate required SMTP settings
            $requiredFields = ['host', 'port', 'username', 'password', 'from_email', 'from_name'];
            $missingFields = [];

            foreach ($requiredFields as $field) {
                if (empty($smtpSettings[$field])) {
                    $missingFields[] = ucfirst(str_replace('_', ' ', $field));
                }
            }

            if (!empty($missingFields)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing SMTP settings: ' . implode(', ', $missingFields)
                ], 400);
            }

            // Configure email
            $email = \Config\Services::email();

            $config = [
                'protocol' => 'smtp',
                'SMTPHost' => $smtpSettings['host'],
                'SMTPPort' => $smtpSettings['port'],
                'SMTPUser' => $smtpSettings['username'],
                'SMTPPass' => $smtpSettings['password'],
                'SMTPCrypto' => $smtpSettings['encryption'] ?: 'tls',
                'mailType' => 'html',
                'charset' => 'utf-8',
                'wordWrap' => true,
            ];

            $email->initialize($config);

            $email->setFrom($smtpSettings['from_email'], $smtpSettings['from_name']);
            $email->setTo($this->request->getPost('recipient_email'));
            $email->setSubject('Test Email - ' . ($settingsModel->getSetting('site_short_name') ?: 'Your Site'));
            $email->setMessage($this->request->getPost('message'));

            if ($email->send()) {
                // Log successful test
                $activityLogger = new ActivityLogger();
                $activityLogger->logAdd('email_tests', 0, 'Test email sent to: ' . $this->request->getPost('recipient_email'));

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Test email sent successfully!'
                ]);
            } else {
                $error = $email->printDebugger(['headers', 'subject', 'body']);
                log_message('error', 'Email test failed: ' . $error);

                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to send test email. Please check your SMTP settings.'
                ], 500);
            }

        } catch (\Exception $e) {
            log_message('error', 'Email test error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while sending the test email: ' . $e->getMessage()
            ], 500);
        }
    }

    public function areas()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        // Check permission
        if (!hasPermission('masters.areas')) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Access denied');
        }

        // Handle CSV export
        if ($this->request->getGet('export') === 'csv') {
            return $this->exportAreasCsv();
        }

        // Get filter parameters
        $filters = [
            'area_name' => $this->request->getGet('area_name'),
            'status' => $this->request->getGet('status'),
            'visible_to_customer' => $this->request->getGet('visible_to_customer'),
            'created_from' => $this->request->getGet('created_from'),
            'created_to' => $this->request->getGet('created_to'),
        ];

        // Build query with filters and society count
        $db = \Config\Database::connect();
        $query = $db->table('areas a')
                    ->select('a.*, COALESCE(CONCAT(ca.first_name, " ", ca.last_name), ca.username) as created_by_name, COALESCE(CONCAT(ua.first_name, " ", ua.last_name), ua.username) as updated_by_name, COALESCE(society_counts.society_count, 0) as society_count')
                    ->join('admins ca', 'ca.id = a.created_by', 'left')
                    ->join('admins ua', 'ua.id = a.updated_by', 'left')
                    ->join('(SELECT area_id, COUNT(*) as society_count FROM societies WHERE deleted_at IS NULL GROUP BY area_id) society_counts', 'society_counts.area_id = a.id', 'left')
                    ->where('a.deleted_at IS NULL');

        // Apply filters
        if (!empty($filters['area_name'])) {
            $query->like('a.area_name', $filters['area_name']);
        }
        if (!empty($filters['status'])) {
            $query->where('a.status', $filters['status']);
        }
        if (!empty($filters['visible_to_customer'])) {
            $query->where('a.visible_to_customer', $filters['visible_to_customer']);
        }
        if (!empty($filters['created_from'])) {
            $query->where('a.created_date >=', $filters['created_from'] . ' 00:00:00');
        }
        if (!empty($filters['created_to'])) {
            $query->where('a.created_date <=', $filters['created_to'] . ' 23:59:59');
        }

        $areas = $query->orderBy('a.id', 'DESC')->get()->getResultArray();

        return view('admin/areas/index', ['areas' => $areas, 'filters' => $filters]);
    }

    public function createArea()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        return view('admin/areas/create');
    }

    public function storeArea()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        $areaModel = new AreaModel();

        // Check for duplicates and partial matches
        $areaName = $this->request->getPost('area_name');
        $areaModel = new AreaModel();

        // Check for exact case-insensitive match
        $existingArea = $areaModel->where('LOWER(area_name)', strtolower($areaName))->first();
        if ($existingArea) {
            return redirect()->back()->withInput()->with('error', 'An area with this name already exists (case-insensitive match).');
        }

        // Check for partial matches (similar names)
        $partialMatches = $areaModel->like('area_name', $areaName, 'both')->findAll();
        if (!empty($partialMatches)) {
            $matchNames = array_column($partialMatches, 'area_name');
            session()->setFlashdata('warning', 'Similar area names found: ' . implode(', ', $matchNames) . '. Do you want to proceed?');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'area_name' => [
                'label' => 'Area Name',
                'rules' => 'required|min_length[2]|max_length[255]',
                'errors' => [
                    'required' => 'Area name is required',
                    'min_length' => 'Area name must be at least 2 characters',
                    'max_length' => 'Area name cannot exceed 255 characters'
                ]
            ],
            'status' => [
                'label' => 'Status',
                'rules' => 'required|in_list[active,inactive]',
                'errors' => [
                    'required' => 'Status is required',
                    'in_list' => 'Status must be either active or inactive'
                ]
            ],
            'visible_to_customer' => [
                'label' => 'Visible to Customer',
                'rules' => 'required|in_list[yes,no]',
                'errors' => [
                    'required' => 'Visible to customer is required',
                    'in_list' => 'Visible to customer must be either yes or no'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'area_name' => $this->request->getPost('area_name'),
            'status' => $this->request->getPost('status'),
            'visible_to_customer' => $this->request->getPost('visible_to_customer')
        ];

        if ($areaModel->insert($data)) {
            $insertedId = $areaModel->getInsertID();
            // Log add activity
            $activityLogger = new ActivityLogger();
            $activityLogger->logAdd('areas', $insertedId, 'Area added: ' . $data['area_name']);

            return redirect()->to(base_url('admin/areas'))->with('success', 'Area created successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create area');
        }
    }

    public function editArea($id)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        $areaModel = new AreaModel();
        $area = $areaModel->find($id);

        if (!$area) {
            return redirect()->to(base_url('admin/areas'))->with('error', 'Area not found');
        }

        return view('admin/areas/edit', ['area' => $area]);
    }

    public function updateArea($id)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        $areaModel = new AreaModel();

        // Check for duplicates and partial matches (excluding current record)
        $areaName = $this->request->getPost('area_name');
        $newStatus = $this->request->getPost('status');

        // Check if trying to disable area with active societies
        if ($newStatus === 'inactive') {
            $societyModel = new \App\Models\SocietyModel();
            $activeSocieties = $societyModel->where('area_id', $id)
                                           ->where('status', 'active')
                                           ->countAllResults();
            if ($activeSocieties > 0) {
                return redirect()->back()->withInput()->with('error', 'Cannot disable this area because it has ' . $activeSocieties . ' active society(ies). Please disable or move the societies first.');
            }
        }

        // Check for exact case-insensitive match (excluding current record)
        $existingArea = $areaModel->where('LOWER(area_name)', strtolower($areaName))
                                  ->where('id !=', $id)
                                  ->first();
        if ($existingArea) {
            return redirect()->back()->withInput()->with('error', 'An area with this name already exists (case-insensitive match).');
        }

        // Check for partial matches (excluding current record)
        $partialMatches = $areaModel->like('area_name', $areaName, 'both')
                                    ->where('id !=', $id)
                                    ->findAll();
        if (!empty($partialMatches)) {
            $matchNames = array_column($partialMatches, 'area_name');
            session()->setFlashdata('warning', 'Similar area names found: ' . implode(', ', $matchNames) . '. Do you want to proceed?');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'area_name' => [
                'label' => 'Area Name',
                'rules' => 'required|min_length[2]|max_length[255]',
                'errors' => [
                    'required' => 'Area name is required',
                    'min_length' => 'Area name must be at least 2 characters',
                    'max_length' => 'Area name cannot exceed 255 characters'
                ]
            ],
            'status' => [
                'label' => 'Status',
                'rules' => 'required|in_list[active,inactive]',
                'errors' => [
                    'required' => 'Status is required',
                    'in_list' => 'Status must be either active or inactive'
                ]
            ],
            'visible_to_customer' => [
                'label' => 'Visible to Customer',
                'rules' => 'required|in_list[yes,no]',
                'errors' => [
                    'required' => 'Visible to customer is required',
                    'in_list' => 'Visible to customer must be either yes or no'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'area_name' => $this->request->getPost('area_name'),
            'status' => $this->request->getPost('status'),
            'visible_to_customer' => $this->request->getPost('visible_to_customer')
        ];

        if ($areaModel->update($id, $data)) {
            // Log edit activity
            $activityLogger = new ActivityLogger();
            $activityLogger->logEdit('areas', $id, 'Area updated: ' . $data['area_name']);

            return redirect()->to(base_url('admin/areas'))->with('success', 'Area updated successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update area');
        }
    }

    public function deleteArea($id)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        $areaModel = new AreaModel();

        if ($areaModel->delete($id)) {
            return redirect()->to(base_url('admin/areas'))->with('success', 'Area deleted successfully');
        } else {
            return redirect()->to(base_url('admin/areas'))->with('error', 'Failed to delete area');
        }
    }

    private function exportAreasCsv()
        {
            // Get filter parameters
            $filters = [
                'area_name' => $this->request->getGet('area_name'),
                'status' => $this->request->getGet('status'),
                'visible_to_customer' => $this->request->getGet('visible_to_customer'),
                'created_from' => $this->request->getGet('created_from'),
                'created_to' => $this->request->getGet('created_to'),
            ];
    
            // Build query with filters and society count (same as areas method)
            $db = \Config\Database::connect();
            $query = $db->table('areas a')
                        ->select('a.*, COALESCE(CONCAT(ca.first_name, " ", ca.last_name), ca.username) as created_by_name, COALESCE(CONCAT(ua.first_name, " ", ua.last_name), ua.username) as updated_by_name, COALESCE(society_counts.society_count, 0) as society_count')
                        ->join('admins ca', 'ca.id = a.created_by', 'left')
                        ->join('admins ua', 'ua.id = a.updated_by', 'left')
                        ->join('(SELECT area_id, COUNT(*) as society_count FROM societies WHERE deleted_at IS NULL GROUP BY area_id) society_counts', 'society_counts.area_id = a.id', 'left')
                        ->where('a.deleted_at IS NULL');
    
            // Apply filters
            if (!empty($filters['area_name'])) {
                $query->like('a.area_name', $filters['area_name']);
            }
            if (!empty($filters['status'])) {
                $query->where('a.status', $filters['status']);
            }
            if (!empty($filters['visible_to_customer'])) {
                $query->where('a.visible_to_customer', $filters['visible_to_customer']);
            }
            if (!empty($filters['created_from'])) {
                $query->where('a.created_date >=', $filters['created_from'] . ' 00:00:00');
            }
            if (!empty($filters['created_to'])) {
                $query->where('a.created_date <=', $filters['created_to'] . ' 23:59:59');
            }
    
            $areas = $query->orderBy('a.id', 'DESC')->get()->getResultArray();
    
            // Set headers for CSV download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="areas_' . date('Y-m-d_H-i-s') . '.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');
    
            // Open output stream
            $output = fopen('php://output', 'w');
    
            // Write CSV headers
            fputcsv($output, [
                'ID',
                'Area Name',
                'Status',
                'Visible to Customer',
                'Societies Count',
                'Created Date',
                'Created By',
                'Last Updated',
                'Updated By'
            ]);
    
            // Write data rows
            foreach ($areas as $area) {
                fputcsv($output, [
                    $area['id'],
                    $area['area_name'],
                    ucfirst($area['status']),
                    ucfirst($area['visible_to_customer']),
                    $area['society_count'] ?? 0,
                    $area['created_date'] ? date('d M Y H:i', strtotime($area['created_date'])) : 'N/A',
                    $area['created_by_name'] ?? 'Unknown',
                    $area['updated_at'] ? date('d M Y H:i', strtotime($area['updated_at'])) : 'Never updated',
                    $area['updated_by_name'] ?? 'N/A'
                ]);
            }
    
            fclose($output);
            exit();
        }
}