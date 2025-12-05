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
            'ip_address' => $this->request->getIPAddress(),
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
}