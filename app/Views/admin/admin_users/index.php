<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>User Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">User Management</h4>
                    <a href="<?= base_url('admin/admin-users/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New User
                    </a>
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

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered datatable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Profile Picture</th>
                                    <th>Username</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>SMS 2FA</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($admins as $admin): ?>
                                    <tr>
                                        <td><?= $admin['id'] ?></td>
                                        <td class="text-center">
                                            <?php if ($admin['profile_picture']): ?>
                                                <img src="<?= base_url('uploads/Admin_Profile/' . $admin['profile_picture']) ?>"
                                                     alt="Profile" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($admin['username']) ?></td>
                                        <td><?= esc($admin['name'] ?? 'N/A') ?></td>
                                        <td><?= esc($admin['email']) ?></td>
                                        <td><?= esc($admin['mobile'] ?? 'N/A') ?></td>
                                        <td>
                                            <span class="badge badge-<?= $admin['role'] === 'super_admin' ? 'danger' : ($admin['role'] === 'admin' ? 'primary' : 'info') ?>">
                                                <?= ucfirst(str_replace('_', ' ', $admin['role'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if (($admin['active'] ?? 1) == 1): ?>
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle"></i> Active
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-times-circle"></i> Inactive
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (($admin['sms_2fa_enabled'] ?? 0) == 1): ?>
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check"></i> Enabled
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-times"></i> Disabled
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $admin['created_at'] ? date('d M Y H:i', strtotime($admin['created_at'])) : 'N/A' ?></td>
                                        <td>
                                            <a href="<?= base_url('admin/admin-users/edit/' . $admin['id']) ?>"
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <?php if (session()->get('admin_role') === 'super_admin' && $admin['id'] !== session()->get('admin_id')): ?>
                                                <button class="btn btn-sm btn-danger ml-1" onclick="changePassword(<?= $admin['id'] ?>, '<?= esc($admin['username']) ?>')">
                                                    <i class="fas fa-key"></i> Password
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Change Password for <span id="userName"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="changePasswordForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="new_password">New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <small class="form-text text-muted">Password must be at least 8 characters with 1 uppercase, 1 lowercase, 1 number, and 1 special character.</small>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="force_password_reset" name="force_password_reset" value="1">
                            <label class="custom-control-label" for="force_password_reset">
                                Force password reset on next login
                            </label>
                        </div>
                        <small class="form-text text-muted">Check this to force the user to change their password on next login.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function changePassword(userId, userName) {
    document.getElementById('userName').textContent = userName;
    document.getElementById('changePasswordForm').action = '<?= base_url('admin/admin-users/update-password/') ?>' + userId;
    $('#changePasswordModal').modal('show');
}

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