<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ImageModel;
use App\Models\SettingsModel;
use App\Libraries\ActivityLogger;

class Images extends Controller
{
    public function __construct()
    {
        helper(['url', 'form', 'text']);
    }

    protected function ensureAuth()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $settingsModel = new SettingsModel();
        $site_logo = $settingsModel->getSetting('site_logo');

        return view('admin/images/index', [
            'site_logo' => $site_logo,
        ]);
    }

    public function getTableData()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        // Get filter parameters
        $filters = [
            'created_from' => $this->request->getGet('created_from'),
            'created_to' => $this->request->getGet('created_to'),
            'updated_from' => $this->request->getGet('updated_from'),
            'updated_to' => $this->request->getGet('updated_to'),
        ];

        // Build query with filters
        $db = \Config\Database::connect();
        $query = $db->table('images i')
                   ->select('i.*, COALESCE(ca.name, ca.username) as created_by_name, COALESCE(ua.name, ua.username) as updated_by_name')
                   ->join('admins ca', 'ca.id = i.created_by', 'left')
                   ->join('admins ua', 'ua.id = i.updated_by', 'left')
                   ->where('i.deleted_at', null);

        // Apply filters
        if (!empty($filters['created_from'])) {
            $query->where('i.created_date >=', $filters['created_from'] . ' 00:00:00');
        }
        if (!empty($filters['created_to'])) {
            $query->where('i.created_date <=', $filters['created_to'] . ' 23:59:59');
        }
        if (!empty($filters['updated_from'])) {
            $query->where('i.updated_date >=', $filters['updated_from'] . ' 00:00:00');
        }
        if (!empty($filters['updated_to'])) {
            $query->where('i.updated_date <=', $filters['updated_to'] . ' 23:59:59');
        }

        $images = $query->orderBy('i.id', 'DESC')->get()->getResultArray();

        // Format data for DataTable
        $data = [];
        foreach ($images as $i => $image) {
            $fileSize = $image['file_size'] ? $this->formatBytes($image['file_size']) : 'N/A';
            $imagePreview = $image['file_path'] ?
                '<img src="' . base_url($image['file_path']) . '" alt="' . esc($image['image_name']) . '" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">' :
                '<span class="text-muted">No Image</span>';

            $data[] = [
                'index' => $i + 1,
                'id' => $image['id'],
                'image_name' => '<strong>' . esc($image['image_name']) . '</strong>',
                'image_preview' => $imagePreview,
                'file_info' => '<small>' . esc($image['file_name']) . '<br>Size: ' . $fileSize . '<br>Type: ' . esc($image['mime_type']) . '</small>',
                'created_by' => '<small><strong>' . esc($image['created_by_name'] ?? 'Unknown') . '</strong><br>' .
                               (!empty($image['created_date']) ? date('d M Y H:i', strtotime($image['created_date'])) : '-') . '</small>',
                'updated_info' => !empty($image['updated_date']) ?
                    '<small><strong>' . esc($image['updated_by_name'] ?? 'Unknown') . '</strong><br>' .
                    date('d M Y H:i', strtotime($image['updated_date'])) . '</small>' :
                    '<small class="text-muted">Never updated</small>',
                'actions' => '<button class="btn btn-sm btn-outline-primary" onclick="editImage(' . $image['id'] . ')"><i class="fas fa-edit"></i> Edit</button>'
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($this->request->getGet('draw')),
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
            'data' => $data
        ]);
    }

    public function getImage($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new ImageModel();
        $image = $model->find($id);

        if (!$image) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Image not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $image
        ]);
    }

    public function store()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'image_name' => ['label' => 'Image Name', 'rules' => 'required|min_length[2]|max_length[255]'],
            'image_file' => [
                'label' => 'Image File',
                'rules' => 'uploaded[image_file]|max_size[image_file,2048]|is_image[image_file]|mime_in[image_file,image/jpg,image/jpeg,image/png,image/gif,image/webp]'
            ],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $model = new ImageModel();
        $file = $this->request->getFile('image_file');

        if (!$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid file upload'
            ]);
        }

        // Create uploads directory if it doesn't exist
        $uploadPath = FCPATH . 'uploads/images';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Get file information before moving
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();
        $fileExtension = $file->getExtension();

        // Generate unique filename
        $newFileName = time() . '_' . random_string('alnum', 8) . '.' . $fileExtension;

        if ($file->move($uploadPath, $newFileName)) {
            $data = [
                'image_name' => trim($this->request->getPost('image_name')),
                'file_name' => $newFileName,
                'file_path' => 'uploads/images/' . $newFileName,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
            ];

            if ($model->insert($data)) {
                $insertedId = $model->getInsertID();
                // Log add activity
                $activityLogger = new ActivityLogger();
                $activityLogger->logAdd('images', $insertedId, 'Image uploaded: ' . $data['image_name']);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Image uploaded successfully'
                ]);
            } else {
                // Delete uploaded file if database insert failed
                if (file_exists($uploadPath . '/' . $newFileName)) {
                    unlink($uploadPath . '/' . $newFileName);
                }
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to save image information'
                ]);
            }
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to upload image file'
            ]);
        }
    }

    public function update($id)
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new ImageModel();
        $image = $model->find($id);

        if (!$image) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Image not found'
            ]);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'image_name' => ['label' => 'Image Name', 'rules' => 'required|min_length[2]|max_length[255]'],
            'image_file' => [
                'label' => 'Image File',
                'rules' => 'max_size[image_file,2048]|is_image[image_file]|mime_in[image_file,image/jpg,image/jpeg,image/png,image/gif,image/webp]',
                'required' => false
            ],
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        $data = [
            'image_name' => trim($this->request->getPost('image_name')),
            'updated_date' => date('Y-m-d H:i:s'),
            'updated_by' => session()->get('admin_id')
        ];

        // Handle file upload if provided
        $file = $this->request->getFile('image_file');
        if ($file && $file->isValid()) {
            $uploadPath = FCPATH . 'uploads/images';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Delete old file
            if (!empty($image['file_path']) && file_exists(FCPATH . $image['file_path'])) {
                unlink(FCPATH . $image['file_path']);
            }

            // Get file information before moving
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();
            $fileExtension = $file->getExtension();

            // Generate unique filename
            $newFileName = time() . '_' . random_string('alnum', 8) . '.' . $fileExtension;

            if ($file->move($uploadPath, $newFileName)) {
                $data['file_name'] = $newFileName;
                $data['file_path'] = 'uploads/images/' . $newFileName;
                $data['file_size'] = $fileSize;
                $data['mime_type'] = $mimeType;
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to upload new image file'
                ]);
            }
        }

        if ($model->update($id, $data)) {
            // Log edit activity
            $activityLogger = new ActivityLogger();
            $activityLogger->logEdit('images', $id, 'Image updated: ' . $data['image_name']);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Image updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update image'
        ]);
    }

    public function getImagesForDropdown()
    {
        if ($redirect = $this->ensureAuth()) return $redirect;

        $model = new ImageModel();
        $images = $model->select('id, image_name, file_path')
                       ->where('status', 'active')
                       ->orderBy('image_name', 'ASC')
                       ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $images
        ]);
    }

    private function formatBytes($bytes, $precision = 2)
    {
        if ($bytes > 0) {
            $i = floor(log($bytes) / log(1024));
            $sizes = array('B', 'KB', 'MB', 'GB', 'TB');
            return round($bytes / pow(1024, $i), $precision) . ' ' . $sizes[$i];
        }
        return '0 B';
    }
}
