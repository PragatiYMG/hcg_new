<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
        }
        .back-btn {
            background-color: #6c757d;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
        }
        .back-btn:hover {
            background-color: #5a6268;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="text"],
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        .form-group input[type="file"] {
            padding: 5px;
        }
        .btn {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #218838;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .current-image {
            max-width: 100px;
            max-height: 100px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Settings</h1>
        <a href="<?= base_url('admin/dashboard') ?>" class="back-btn">Back to Dashboard</a>
    </div>
    <div class="container">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/settings/update') ?>" method="post" enctype="multipart/form-data">
            <div class="form-group row mb-4">
                            <label class="col-md-3 col-form-label">Site Logo</label>
                            <div class="col-md-9">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="logo" name="logo" accept="image/*">
                                    <label class="custom-file-label" for="logo">Choose logo file (max 2MB)</label>
                                </div>
                                <small class="form-text text-muted">Recommended size: 180x50px. Formats: JPG, PNG, WebP</small>
                                <?php if (!empty($site_logo) && file_exists(ROOTPATH . 'public/uploads/' . $site_logo)): ?>
                                <div class="mt-2">
                                    <p>Current Logo:</p>
                                    <img src="<?= base_url('uploads/' . $site_logo) ?>" alt="Current Logo" class="current-image">
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-md-3 col-form-label">Favicon</label>
                            <div class="col-md-9">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="favicon" name="favicon" accept=".ico,image/x-icon,image/vnd.microsoft.icon,image/png">
                                    <label class="custom-file-label" for="favicon">Choose favicon file (max 1MB)</label>
                                </div>
                                <small class="form-text text-muted">Recommended size: 32x32px or 64x64px. Formats: ICO, PNG, JPG</small>
                                <?php if (!empty($site_favicon) && file_exists(ROOTPATH . 'public/uploads/' . $site_favicon)): ?>
                                <div class="mt-2">
                                    <p>Current Favicon:</p>
                                    <img src="<?= base_url('uploads/' . $site_favicon) ?>" alt="Current Favicon" class="current-image">
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn">Update Settings</button>
                            </div>
                        </div>

            
        </form>
    </div>
</body>
</html>