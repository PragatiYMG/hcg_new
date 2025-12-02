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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?= esc($admin['username']) ?>" readonly>
                                    <small class="form-text text-muted">Username cannot be changed.</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= isset(session()->getFlashdata('errors')['name']) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= old('name', esc($admin['name'] ?? '')) ?>" required>
                                    <?php if (isset(session()->getFlashdata('errors')['name'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('errors')['name'] ?>
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
</script>
<?= $this->endSection() ?>