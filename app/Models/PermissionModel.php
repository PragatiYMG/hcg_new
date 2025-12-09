<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table            = 'permissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'display_name', 'description', 'module'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'name' => 'required|is_unique[permissions.name,id,{id}]|max_length[100]',
        'display_name' => 'required|max_length[255]',
        'module' => 'required|max_length[100]'
    ];
    protected $validationMessages   = [
        'name' => [
            'required' => 'Permission name is required',
            'is_unique' => 'Permission name must be unique',
            'max_length' => 'Permission name cannot exceed 100 characters'
        ],
        'display_name' => [
            'required' => 'Display name is required',
            'max_length' => 'Display name cannot exceed 255 characters'
        ],
        'module' => [
            'required' => 'Module is required',
            'max_length' => 'Module cannot exceed 100 characters'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get permissions grouped by module
     */
    public function getGroupedByModule()
    {
        $permissions = $this->orderBy('module', 'ASC')->orderBy('display_name', 'ASC')->findAll();

        $grouped = [];
        foreach ($permissions as $permission) {
            $grouped[$permission['module']][] = $permission;
        }

        return $grouped;
    }

    /**
     * Get permissions for a specific role
     */
    public function getRolePermissions($role)
    {
        return $this->db->table('role_permissions rp')
                       ->join('permissions p', 'p.id = rp.permission_id')
                       ->where('rp.role', $role)
                       ->get()
                       ->getResultArray();
    }

    /**
     * Get permissions for a specific user (only user-specific permissions)
     */
    public function getUserPermissions($userId)
    {
        $user = $this->db->table('admins')->where('id', $userId)->get()->getRowArray();

        if (!$user) {
            return [];
        }

        // Super admin has all permissions
        if ($user['role'] === 'super_admin') {
            return $this->findAll();
        }

        // Get only user-specific permissions
        return $this->db->table('user_permissions up')
                        ->join('permissions p', 'p.id = up.permission_id')
                        ->where('up.user_id', $userId)
                        ->where('up.granted', 1)
                        ->get()
                        ->getResultArray();
    }

    /**
     * Check if user has a specific permission
     */
    public function userHasPermission($userId, $permissionName)
    {
        $user = $this->db->table('admins')->where('id', $userId)->get()->getRowArray();

        if (!$user) {
            return false;
        }

        // Super admin has all permissions
        if ($user['role'] === 'super_admin') {
            return true;
        }

        // For all other roles, check only user-specific permissions
        $userPermission = $this->db->table('user_permissions up')
                                   ->join('permissions p', 'p.id = up.permission_id')
                                   ->where('up.user_id', $userId)
                                   ->where('p.name', $permissionName)
                                   ->where('up.granted', 1)
                                   ->get()
                                   ->getRowArray();

        return $userPermission !== null;
    }
}