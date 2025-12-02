<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>Create User<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Create New User</h4>
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

                    <form action="<?= base_url('admin/admin-users/store') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= isset(session()->getFlashdata('errors')['username']) ? 'is-invalid' : '' ?>" id="username" name="username" value="<?= old('username') ?>" required>
                                    <?php if (isset(session()->getFlashdata('errors')['username'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('errors')['username'] ?>
                                        </div>
                                    <?php endif; ?>
                                    <small class="form-text text-muted">Username cannot be changed later.</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= isset(session()->getFlashdata('errors')['name']) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= old('name') ?>" required>
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
                                    <input type="email" class="form-control <?= isset(session()->getFlashdata('errors')['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= old('email') ?>" required>
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
                                        <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Admin</option>
                                        <option value="employee" <?= old('role') === 'employee' ? 'selected' : '' ?>>Employee</option>
                                        <option value="super_admin" <?= old('role') === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                                    </select>
                                    <?php if (isset(session()->getFlashdata('errors')['role'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('errors')['role'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control <?= isset(session()->getFlashdata('errors')['password']) ? 'is-invalid' : '' ?>" id="password" name="password" required>
                                    <small class="form-text text-muted">Password must be at least 8 characters with 1 uppercase, 1 lowercase, 1 number, and 1 special character.</small>
                                    <?php if (isset(session()->getFlashdata('errors')['password'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('errors')['password'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="confirm_password">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control <?= isset(session()->getFlashdata('errors')['confirm_password']) ? 'is-invalid' : '' ?>" id="confirm_password" name="confirm_password" required>
                                    <?php if (isset(session()->getFlashdata('errors')['confirm_password'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('errors')['confirm_password'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="profile_picture">Profile Picture</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input <?= isset(session()->getFlashdata('errors')['profile_picture']) ? 'is-invalid' : '' ?>" id="profile_picture" name="profile_picture" accept="image/*">
                                <label class="custom-file-label" for="profile_picture">Choose file...</label>
                                <?php if (isset(session()->getFlashdata('errors')['profile_picture'])): ?>
                                    <div class="invalid-feedback">
                                        <?= session()->getFlashdata('errors')['profile_picture'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <small class="form-text text-muted">Allowed formats: JPG, JPEG, PNG, WebP. Max size: 2MB</small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create User
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