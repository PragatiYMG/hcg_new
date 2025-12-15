<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table            = 'customers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'date_of_birth',
        'father_husband_name',
        'mother_name',
        'primary_mobile',
        'alternate_mobile',
        'email',
        'customer_photo',
        'aadhaar_number',
        'aadhaar_attachment',
        'secondary_id_type',
        'secondary_id_number',
        'secondary_id_attachment',
        'status',
        'email_verified',
        'mobile_verified',
        'created_by',
        'updated_by'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'first_name' => [
            'label' => 'First Name',
            'rules' => 'required|min_length[2]|max_length[100]|alpha_space'
        ],
        'middle_name' => [
            'label' => 'Middle Name',
            'rules' => 'max_length[100]|alpha_space'
        ],
        'last_name' => [
            'label' => 'Last Name',
            'rules' => 'required|min_length[2]|max_length[100]|alpha_space'
        ],
        'gender' => [
            'label' => 'Gender',
            'rules' => 'required|in_list[male,female,other]'
        ],
        'date_of_birth' => [
            'label' => 'Date of Birth',
            'rules' => 'required|valid_date'
        ],
        'father_husband_name' => [
            'label' => 'Father/Husband Name',
            'rules' => 'required|min_length[2]|max_length[255]|alpha_space'
        ],
        'mother_name' => [
            'label' => 'Mother Name',
            'rules' => 'max_length[255]|alpha_space'
        ],
        'primary_mobile' => [
            'label' => 'Primary Mobile',
            'rules' => 'required|exact_length[10]|numeric|is_unique[customers.primary_mobile,id,{id}]'
        ],
        'alternate_mobile' => [
            'label' => 'Alternate Mobile',
            'rules' => 'exact_length[10]|numeric|is_unique[customers.alternate_mobile,id,{id}]'
        ],
        'email' => [
            'label' => 'Email',
            'rules' => 'required|valid_email|max_length[255]|is_unique[customers.email,id,{id}]'
        ],
        'aadhaar_number' => [
            'label' => 'Aadhaar Number',
            'rules' => 'required|exact_length[12]|numeric|is_unique[customers.aadhaar_number,id,{id}]'
        ],
        'secondary_id_type' => [
            'label' => 'Secondary ID Type',
            'rules' => 'in_list[voter_id,passport,driving_license]'
        ],
        'secondary_id_number' => [
            'label' => 'Secondary ID Number',
            'rules' => 'max_length[50]'
        ]
    ];

    protected $validationMessages   = [
        'first_name' => [
            'required' => 'First name is required',
            'min_length' => 'First name must be at least 2 characters',
            'max_length' => 'First name cannot exceed 100 characters',
            'alpha_space' => 'First name can only contain letters and spaces'
        ],
        'last_name' => [
            'required' => 'Last name is required',
            'min_length' => 'Last name must be at least 2 characters',
            'max_length' => 'Last name cannot exceed 100 characters',
            'alpha_space' => 'Last name can only contain letters and spaces'
        ],
        'gender' => [
            'required' => 'Gender is required',
            'in_list' => 'Gender must be Male, Female, or Other'
        ],
        'date_of_birth' => [
            'required' => 'Date of birth is required',
            'valid_date' => 'Please enter a valid date'
        ],
        'father_husband_name' => [
            'required' => 'Father/Husband name is required',
            'min_length' => 'Name must be at least 2 characters',
            'max_length' => 'Name cannot exceed 255 characters',
            'alpha_space' => 'Name can only contain letters and spaces'
        ],
        'primary_mobile' => [
            'required' => 'Primary mobile number is required',
            'exact_length' => 'Mobile number must be exactly 10 digits',
            'numeric' => 'Mobile number must contain only digits',
            'is_unique' => 'This mobile number is already registered'
        ],
        'alternate_mobile' => [
            'exact_length' => 'Mobile number must be exactly 10 digits',
            'numeric' => 'Mobile number must contain only digits',
            'is_unique' => 'This mobile number is already registered'
        ],
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please enter a valid email address',
            'max_length' => 'Email cannot exceed 255 characters',
            'is_unique' => 'This email is already registered'
        ],
        'aadhaar_number' => [
            'required' => 'Aadhaar number is required',
            'exact_length' => 'Aadhaar number must be exactly 12 digits',
            'numeric' => 'Aadhaar number must contain only digits',
            'is_unique' => 'This Aadhaar number is already registered'
        ],
        'secondary_id_type' => [
            'in_list' => 'Secondary ID type must be Voter ID, Passport, or Driving License'
        ],
        'secondary_id_number' => [
            'max_length' => 'Secondary ID number cannot exceed 50 characters'
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
}
