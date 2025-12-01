<?php

namespace App\Models;

use CodeIgniter\Model;

class RateModel extends Model
{
    protected $table            = 'rates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'basic_rate',
        'full_rate',
        'effective_date',
        'status',
        'created_date',
        'created_by',
        'updated_date',
        'updated_by',
    ];

    protected bool $allowEmptyInserts = false;

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_date';
    protected $updatedField  = 'updated_date';
    protected $deletedField  = 'deleted_at';

    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setCreatedFields'];
    protected $beforeUpdate   = ['setUpdatedFields'];

    protected function setCreatedFields(array $data)
    {
        $data['data']['created_by'] = session()->get('admin_id');
        $data['data']['created_date'] = date('Y-m-d H:i:s');
        return $data;
    }

    protected function setUpdatedFields(array $data)
    {
        $data['data']['updated_by'] = session()->get('admin_id');
        $data['data']['updated_date'] = date('Y-m-d H:i:s');
        return $data;
    }

    /**
     * Get the currently active rate
     */
    public function getActiveRate()
    {
        return $this->where('status', 'active')
                    ->where('effective_date <=', date('Y-m-d'))
                    ->orderBy('effective_date', 'DESC')
                    ->first();
    }
}