<?php

namespace App\Libraries;

use App\Models\ActivityLogModel;
use CodeIgniter\HTTP\RequestInterface;

class ActivityLogger
{
    protected $activityLogModel;
    protected $request;

    public function __construct()
    {
        $this->activityLogModel = new ActivityLogModel();
        $this->request = service('request');
    }

    public function log($action, $tableName = null, $recordId = null, $description = null, $userId = null)
    {
        $data = [
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'description' => $description,
            'ip_address' => $this->getClientIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if ($userId === null) {
            $userId = session()->get('admin_id'); // Assuming admin_id is stored in session
        }

        $data['user_id'] = $userId;

        $this->activityLogModel->insert($data);
    }

    public function logAdd($tableName, $recordId, $description = null, $userId = null)
    {
        $this->log('add', $tableName, $recordId, $description, $userId);
    }

    public function logEdit($tableName, $recordId, $description = null, $userId = null)
    {
        $this->log('edit', $tableName, $recordId, $description, $userId);
    }

    public function logLogin($userId = null, $description = null)
    {
        $this->log('login', null, null, $description, $userId);
    }

    /**
     * Get the real client IP address, considering proxy headers
     */
    protected function getClientIPAddress()
    {
        // Check for common proxy headers in order of preference
        $proxyHeaders = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED'
        ];

        foreach ($proxyHeaders as $header) {
            if (!empty($_SERVER[$header])) {
                // X-Forwarded-For can contain multiple IPs, take the first one
                $ip = trim(explode(',', $_SERVER[$header])[0]);

                // Validate IP address
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        // Fall back to CodeIgniter's method
        return $this->request->getIPAddress();
    }
}