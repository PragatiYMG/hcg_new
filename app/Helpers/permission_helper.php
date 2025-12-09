<?php

if (!function_exists('hasPermission')) {
    /**
     * Check if current user has a specific permission
     *
     * @param string $permission Permission name (e.g., 'users.view')
     * @param int|null $userId User ID (optional, defaults to current user)
     * @return bool
     */
    function hasPermission($permission, $userId = null)
    {
        // Super admin has all permissions
        if (isSuperAdmin($userId)) {
            return true;
        }

        $permissionModel = new \App\Models\PermissionModel();

        if ($userId === null) {
            $userId = session()->get('admin_id');
        }

        return $permissionModel->userHasPermission($userId, $permission);
    }
}

if (!function_exists('canAccess')) {
    /**
     * Check if current user can access a module/action
     * Alias for hasPermission for better readability
     *
     * @param string $permission Permission name
     * @param int|null $userId User ID (optional)
     * @return bool
     */
    function canAccess($permission, $userId = null)
    {
        return hasPermission($permission, $userId);
    }
}

if (!function_exists('isSuperAdmin')) {
    /**
     * Check if current user is super admin
     *
     * @param int|null $userId User ID (optional)
     * @return bool
     */
    function isSuperAdmin($userId = null)
    {
        if ($userId === null) {
            return session()->get('admin_role') === 'super_admin';
        }

        $adminModel = new \App\Models\AdminModel();
        $admin = $adminModel->find($userId);
        return $admin && $admin['role'] === 'super_admin';
    }
}

if (!function_exists('requirePermission')) {
    /**
     * Require a permission or redirect with error
     *
     * @param string $permission Permission name
     * @param string $redirectUrl Redirect URL if permission denied
     * @return void
     */
    function requirePermission($permission, $redirectUrl = null)
    {
        if (!hasPermission($permission)) {
            $message = isSuperAdmin()
                ? 'Super admin access required.'
                : 'You do not have permission to access this feature.';

            if ($redirectUrl) {
                return redirect()->to($redirectUrl)->with('error', $message);
            } else {
                return redirect()->back()->with('error', $message);
            }
        }
    }
}

if (!function_exists('getUserPermissions')) {
    /**
     * Get all permissions for current user
     *
     * @param int|null $userId User ID (optional)
     * @return array
     */
    function getUserPermissions($userId = null)
    {
        $permissionModel = new \App\Models\PermissionModel();

        if ($userId === null) {
            $userId = session()->get('admin_id');
        }

        return $permissionModel->getUserPermissions($userId);
    }
}