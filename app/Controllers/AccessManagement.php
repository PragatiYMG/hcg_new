<?php

namespace App\Controllers;

use App\Models\PermissionModel;
use App\Models\AdminModel;
use CodeIgniter\Controller;

class AccessManagement extends Controller
{
    public function __construct()
    {
        helper('url');
    }

    /**
     * Main access management dashboard
     */
    public function index()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        // Only super admin can access
        if (session()->get('admin_role') !== 'super_admin') {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Super admin access required.');
        }

        $permissionModel = new PermissionModel();
        $adminModel = new AdminModel();

        $data = [
            'permissions' => $permissionModel->getGroupedByModule(),
            'roles' => ['super_admin', 'admin', 'employee'],
            'users' => $adminModel->findAll()
        ];

        return view('admin/access_management/index', $data);
    }

    /**
     * Update role permissions
     */
    public function updateRolePermissions()
    {
        if (!session()->get('admin_logged_in') || session()->get('admin_role') !== 'super_admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $role = $this->request->getPost('role');
        $permissions = $this->request->getPost('permissions') ?? [];

        if (!in_array($role, ['super_admin', 'admin', 'employee'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid role']);
        }

        $db = \Config\Database::connect();

        // Start transaction
        $db->transStart();

        try {
            // Delete existing role permissions
            $db->table('role_permissions')->where('role', $role)->delete();

            // Insert new permissions
            if (!empty($permissions)) {
                $rolePermissions = [];
                foreach ($permissions as $permissionId) {
                    $rolePermissions[] = [
                        'role' => $role,
                        'permission_id' => $permissionId
                    ];
                }
                $db->table('role_permissions')->insertBatch($rolePermissions);
            }

            $db->transComplete();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Role permissions updated successfully'
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update role permissions'
            ]);
        }
    }

    /**
     * Update user permissions
     */
    public function updateUserPermissions()
    {
        if (!session()->get('admin_logged_in') || session()->get('admin_role') !== 'super_admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = $this->request->getPost('user_id');
        $permissions = $this->request->getPost('permissions') ?? [];

        $adminModel = new AdminModel();
        $user = $adminModel->find($userId);

        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
        }

        $db = \Config\Database::connect();

        // Start transaction
        $db->transStart();

        try {
            // Delete existing user permissions
            $db->table('user_permissions')->where('user_id', $userId)->delete();

            // Insert new permissions
            if (!empty($permissions)) {
                $userPermissions = [];
                foreach ($permissions as $permissionData) {
                    $userPermissions[] = [
                        'user_id' => $userId,
                        'permission_id' => $permissionData['id'],
                        'granted' => $permissionData['granted'] ? 1 : 0
                    ];
                }
                $db->table('user_permissions')->insertBatch($userPermissions);
            }

            $db->transComplete();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'User permissions updated successfully'
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update user permissions'
            ]);
        }
    }

    /**
     * Get role permissions (AJAX)
     */
    public function getRolePermissions()
    {
        if (!session()->get('admin_logged_in') || session()->get('admin_role') !== 'super_admin') {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $role = $this->request->getGet('role');

        if (!in_array($role, ['super_admin', 'admin', 'employee'])) {
            return $this->response->setJSON(['error' => 'Invalid role']);
        }

        $permissionModel = new PermissionModel();
        $permissions = $permissionModel->getRolePermissions($role);

        $permissionIds = array_column($permissions, 'id');

        return $this->response->setJSON([
            'permissions' => $permissionIds
        ]);
    }

    /**
     * Get user permissions (AJAX)
     */
    public function getUserPermissions()
    {
        if (!session()->get('admin_logged_in') || session()->get('admin_role') !== 'super_admin') {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $userId = $this->request->getGet('user_id');

        $permissionModel = new PermissionModel();
        $permissions = $permissionModel->getUserPermissions($userId);

        $result = [];
        foreach ($permissions as $permission) {
            $result[] = [
                'id' => $permission['id'],
                'name' => $permission['name'],
                'granted' => true // All returned permissions are granted
            ];
        }

        return $this->response->setJSON([
            'permissions' => $result
        ]);
    }
}
