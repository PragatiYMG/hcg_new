<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingsModel extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['key', 'value'];
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get a setting value by key
     */
    public function getSetting($key, $default = null)
    {
        $setting = $this->where('key', $key)->first();
        return $setting ? $setting['value'] : $default;
    }

    /**
     * Set a setting value by key
     */
    public function setSetting($key, $value = null)
    {
        $setting = $this->where('key', $key)->first();
        
        if ($setting) {
            return $this->update($setting['id'], [
                'value' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        return $this->insert([
            'key' => $key,
            'value' => $value,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
