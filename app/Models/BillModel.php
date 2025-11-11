<?php

namespace App\Models;

use CodeIgniter\Model;

class BillModel extends Model
{
    protected $table            = 'bills';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'name',
        'tag_line',
        'active',
        'address',
        'phone',
        'emergency_no',
        'email',
        'tin',
        'website',
        'summary_description',
        'footer_description',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    protected bool $allowEmptyInserts = false;

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedFields'];
    protected $beforeUpdate   = ['setUpdatedFields'];

    protected function setCreatedFields(array $data)
    {
        $data['data']['created_by'] = session()->get('admin_id');
        $data['data']['created_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    protected function setUpdatedFields(array $data)
    {
        $data['data']['updated_by'] = session()->get('admin_id');
        $data['data']['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }
}