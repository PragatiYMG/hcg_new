<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>Edit User<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Edit User</h4>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show auto-hide" role="alert">
                            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/admin-users/update/' . $admin['id']) ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?= esc($admin['username']) ?>" readonly>
                                    <small class="form-text text-muted">Username cannot be changed.</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= isset(session()->getFlashdata('errors')['first_name']) ? 'is-invalid' : '' ?>" id="first_name" name="first_name" value="<?= old('first_name', esc($admin['first_name'] ?? '')) ?>" required>
                                    <?php if (isset(session()->getFlashdata('errors')['first_name'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('errors')['first_name'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= isset(session()->getFlashdata('errors')['last_name']) ? 'is-invalid' : '' ?>" id="last_name" name="last_name" value="<?= old('last_name', esc($admin['last_name'] ?? '')) ?>" required>
                                    <?php if (isset(session()->getFlashdata('errors')['last_name'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('errors')['last_name'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control <?= isset(session()->getFlashdata('errors')['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= old('email', esc($admin['email'])) ?>" required>
                                    <?php if (isset(session()->getFlashdata('errors')['email'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('errors')['email'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mobile">Mobile Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= isset(session()->getFlashdata('errors')['mobile']) ? 'is-invalid' : '' ?>" id="mobile" name="mobile" value="<?= old('mobile', esc($admin['mobile'] ?? '')) ?>" placeholder="e.g., 9876543210" required>
                                    <?php if (isset(session()->getFlashdata('errors')['mobile'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('errors')['mobile'] ?>
                                        </div>
                                    <?php endif; ?>
                                    <small class="form-text text-muted">Must be a valid 10-digit mobile number starting with 6, 7, 8, or 9.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="role">Role <span class="text-danger">*</span></label>
                                    <select class="form-control <?= isset(session()->getFlashdata('errors')['role']) ? 'is-invalid' : '' ?>" id="role" name="role" required>
                                        <option value="">Select Role</option>
                                        <option value="admin" <?= old('role', $admin['role']) === 'admin' ? 'selected' : '' ?>>Admin</option>
                                        <option value="employee" <?= old('role', $admin['role']) === 'employee' ? 'selected' : '' ?>>Employee</option>
                                        <option value="super_admin" <?= old('role', $admin['role']) === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                                    </select>
                                    <?php if (isset(session()->getFlashdata('errors')['role'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('errors')['role'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="department_id">Department</label>
                                    <select class="form-control <?= isset(session()->getFlashdata('errors')['department_id']) ? 'is-invalid' : '' ?>" id="department_id" name="department_id">
                                        <option value="">Select Department</option>
                                        <?php if (!empty($departments)): foreach ($departments as $dept): ?>
                                            <option value="<?= esc($dept['id']) ?>" <?= old('department_id', $admin['department_id'] ?? '') == $dept['id'] ? 'selected' : '' ?>><?= esc($dept['department_name']) ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                    <?php if (isset(session()->getFlashdata('errors')['department_id'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('errors')['department_id'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sms_2fa_enabled">SMS 2FA <span class="text-danger">*</span></label>
                                    <select class="form-control <?= isset(session()->getFlashdata('errors')['sms_2fa_enabled']) ? 'is-invalid' : '' ?>" id="sms_2fa_enabled" name="sms_2fa_enabled" required>
                                        <option value="0" <?= old('sms_2fa_enabled', $admin['sms_2fa_enabled'] ?? 0) == 0 ? 'selected' : '' ?>>Disabled</option>
                                        <option value="1" <?= old('sms_2fa_enabled', $admin['sms_2fa_enabled'] ?? 0) == 1 ? 'selected' : '' ?>>Enabled</option>
                                    </select>
                                    <?php if (isset(session()->getFlashdata('errors')['sms_2fa_enabled'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('errors')['sms_2fa_enabled'] ?>
                                        </div>
                                    <?php endif; ?>
                                    <small class="form-text text-muted">Enable SMS-based two-factor authentication for this user.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="force_password_reset">Force Password Reset <span class="text-danger">*</span></label>
                                    <select class="form-control <?= isset(session()->getFlashdata('errors')['force_password_reset']) ? 'is-invalid' : '' ?>" id="force_password_reset" name="force_password_reset" required>
                                        <option value="0" <?= old('force_password_reset', $admin['force_password_reset'] ?? 0) == 0 ? 'selected' : '' ?>>No</option>
                                        <option value="1" <?= old('force_password_reset', $admin['force_password_reset'] ?? 0) == 1 ? 'selected' : '' ?>>Yes</option>
                                    </select>
                                    <?php if (isset(session()->getFlashdata('errors')['force_password_reset'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('errors')['force_password_reset'] ?>
                                        </div>
                                    <?php endif; ?>
                                    <small class="form-text text-muted">Force this user to change their password on next login.</small>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="form-group">
                                    <label for="active">Active Status <span class="text-danger">*</span></label>
                                    <select class="form-control <?= isset(session()->getFlashdata('errors')['active']) ? 'is-invalid' : '' ?>" id="active" name="active" required>
                                        <option value="1" <?= old('active', $admin['active'] ?? 1) == 1 ? 'selected' : '' ?>>Active</option>
                                        <option value="0" <?= old('active', $admin['active'] ?? 1) == 0 ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                    <?php if (isset(session()->getFlashdata('errors')['active'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('errors')['active'] ?>
                                        </div>
                                    <?php endif; ?>
                                    <small class="form-text text-muted">Set the account status. Inactive users cannot login.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                           
                        </div>

                        <!-- Profile Picture Section -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Current Profile Picture</label>
                                    <div class="mb-2">
                                        <?php if ($admin['profile_picture']): ?>
                                            <img src="<?= base_url('uploads/Admin_Profile/' . $admin['profile_picture']) ?>"
                                                 alt="Current Profile" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover; border: 2px solid #ddd;">
                                        <?php else: ?>
                                            <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
                                                 style="width: 80px; height: 80px;">
                                                <i class="fas fa-user text-white fa-2x"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="profile_picture">Change Profile Picture</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input <?= isset(session()->getFlashdata('errors')['profile_picture']) ? 'is-invalid' : '' ?>" id="profile_picture" name="profile_picture" accept="image/*">
                                        <label class="custom-file-label" for="profile_picture">Choose file...</label>
                                        <?php if (isset(session()->getFlashdata('errors')['profile_picture'])): ?>
                                            <div class="invalid-feedback">
                                                <?= session()->getFlashdata('errors')['profile_picture'] ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <small class="form-text text-muted">Leave empty to keep current picture. Allowed formats: JPG, JPEG, PNG, WebP. Max size: 2MB</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update User
                            </button>
                            <a href="<?= base_url('admin/admin-users') ?>" class="btn btn-secondary ml-2">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update file input label when file is selected
document.getElementById('profile_picture').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name || 'Choose file...';
    const label = e.target.nextElementSibling;
    label.textContent = fileName;
});

// Auto-hide success notifications
$(document).ready(function() {
    $('.alert-success.auto-hide').each(function() {
        var $alert = $(this);
        setTimeout(function() {
            $alert.fadeOut('slow', function() {
                $alert.remove();
            });
        }, 5000); // Hide after 5 seconds
    });
});
</script>
<?= $this->endSection() ?>