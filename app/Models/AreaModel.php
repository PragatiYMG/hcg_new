<?php

namespace App\Models;

use CodeIgniter\Model;

class AreaModel extends Model
{
    protected $table            = 'areas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['area_name', 'status', 'visible_to_customer', 'created_date', 'created_by', 'updated_by'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_date';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedFields'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['setUpdatedFields'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function setCreatedFields(array $data)
    {
        $data['data']['created_by'] = session()->get('admin_id');
        $data['data']['created_date'] = date('Y-m-d H:i:s');
        return $data;
    }

    protected function setUpdatedFields(array $data)
    {
        $data['data']['updated_by'] = session()->get('admin_id');
        return $data;
    }
}
