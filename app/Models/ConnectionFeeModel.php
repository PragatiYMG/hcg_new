<?php

namespace App\Models;

use CodeIgniter\Model;

class ConnectionFeeModel extends Model
{
    protected $table            = 'connection_fees';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'total_fee',
        'refundable_fee',
        'non_refundable_fee',
        'effective_date',
        'status',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
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

    /**
     * Get the currently active connection fee
     */
    public function getActiveFee()
    {
        return $this->where('status', 'active')
                    ->where('effective_date <=', date('Y-m-d'))
                    ->orderBy('effective_date', 'DESC')
                    ->first();
    }
}
