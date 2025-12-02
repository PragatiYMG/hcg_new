<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>Profile<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">My Profile</h4>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
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

                    <div class="row">
                        <!-- Profile Picture Section -->
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="mb-3">
                                    <?php
                                    $profilePic = isset($admin['profile_picture']) && $admin['profile_picture']
                                        ? base_url('uploads/Admin_Profile/' . $admin['profile_picture'])
                                        : base_url('https://via.placeholder.com/150x150?text=No+Image');
                                    ?>
                                    <img src="<?= esc($profilePic) ?>" alt="Profile Picture" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #ddd;">
                                </div>
                            </div>
                        </div>

                        <!-- Profile Information -->
                        <div class="col-md-8">
                            <form action="<?= base_url('admin/profile/update') ?>" method="post" enctype="multipart/form-data">
                                <?= csrf_field() ?>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="username">Username <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control <?= isset(session()->getFlashdata('errors')['username']) ? 'is-invalid' : '' ?>" id="username" name="username" value="<?= old('username', esc($admin['username'])) ?>" required>
                                            <?php if (isset(session()->getFlashdata('errors')['username'])): ?>
                                                <div class="invalid-feedback">
                                                    <?= session()->getFlashdata('errors')['username'] ?>
                                                </div>
                                            <?php endif; ?>
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

                                <div class="form-group">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control <?= isset(session()->getFlashdata('errors')['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= old('email', esc($admin['email'])) ?>" required>
                                    <?php if (isset(session()->getFlashdata('errors')['email'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('errors')['email'] ?>
                                        </div>
                                    <?php endif; ?>
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

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Profile
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">Change Password</h4>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('password_success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('password_success') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('password_errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('password_errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/profile/change-password') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="current_password">Current Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control <?= isset(session()->getFlashdata('password_errors')['current_password']) ? 'is-invalid' : '' ?>" id="current_password" name="current_password" required>
                                    <?php if (isset(session()->getFlashdata('password_errors')['current_password'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('password_errors')['current_password'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="new_password">New Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control <?= isset(session()->getFlashdata('password_errors')['new_password']) ? 'is-invalid' : '' ?>" id="new_password" name="new_password" required>
                                    <small class="form-text text-muted">Password must be at least 8 characters with 1 uppercase, 1 lowercase, 1 number, and 1 special character.</small>
                                    <?php if (isset(session()->getFlashdata('password_errors')['new_password'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('password_errors')['new_password'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="confirm_password">Confirm New Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control <?= isset(session()->getFlashdata('password_errors')['confirm_password']) ? 'is-invalid' : '' ?>" id="confirm_password" name="confirm_password" required>
                                    <?php if (isset(session()->getFlashdata('password_errors')['confirm_password'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('password_errors')['confirm_password'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key"></i> Change Password
                        </button>
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