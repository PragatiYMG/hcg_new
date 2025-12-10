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

                    <!-- Filter Form -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <button class="btn btn-link p-0" type="button" data-toggle="collapse" data-target="#filterCollapse" aria-expanded="false">
                                    <i class="fas fa-filter"></i> Filters
                                </button>
                            </h5>
                        </div>
                        <div class="collapse" id="filterCollapse">
                            <div class="card-body">
                                <form id="filterForm" class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter_username">Username</label>
                                            <input type="text" class="form-control" id="filter_username" name="username" placeholder="Search username">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter_first_name">First Name</label>
                                            <input type="text" class="form-control" id="filter_first_name" name="first_name" placeholder="Search first name">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter_last_name">Last Name</label>
                                            <input type="text" class="form-control" id="filter_last_name" name="last_name" placeholder="Search last name">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter_department_id">Department</label>
                                            <select class="form-control" id="filter_department_id" name="department_id">
                                                <option value="">All Departments</option>
                                                <?php if (!empty($departments)): foreach ($departments as $dept): ?>
                                                    <option value="<?= esc($dept['id']) ?>"><?= esc($dept['department_name']) ?></option>
                                                <?php endforeach; endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter_mobile">Mobile</label>
                                            <input type="text" class="form-control" id="filter_mobile" name="mobile" placeholder="Search mobile">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter_email">Email</label>
                                            <input type="email" class="form-control" id="filter_email" name="email" placeholder="Search email">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter_active">Status</label>
                                            <select class="form-control" id="filter_active" name="active">
                                                <option value="">All Status</option>
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter_sms_2fa_enabled">SMS 2FA</label>
                                            <select class="form-control" id="filter_sms_2fa_enabled" name="sms_2fa_enabled">
                                                <option value="">All</option>
                                                <option value="1">Enabled</option>
                                                <option value="0">Disabled</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-primary" onclick="applyFilters()">
                                            <i class="fas fa-search"></i> Apply Filters
                                        </button>
                                        <button type="button" class="btn btn-secondary ml-2" onclick="clearFilters()">
                                            <i class="fas fa-times"></i> Clear Filters
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Profile Picture</th>
                                    <th>Username</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Department</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>SMS 2FA</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="adminTableBody">
                                <!-- Data will be loaded via AJAX -->
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
let currentFilters = {};

function loadAdminUsers() {
    const tbody = document.getElementById('adminTableBody');
    tbody.innerHTML = '<tr><td colspan="12" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';

    const formData = new FormData(document.getElementById('filterForm'));
    const params = new URLSearchParams();

    for (let [key, value] of formData.entries()) {
        if (value.trim() !== '') {
            params.append(key, value);
        }
    }

    fetch('<?= base_url('admin/admin-users/get-table-data') ?>?' + params.toString(), {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            renderAdminTable(data.data);
        } else {
            console.error('Error loading data:', data);
            tbody.innerHTML = '<tr><td colspan="12" class="text-center text-danger">Error loading data. Please try again.</td></tr>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        tbody.innerHTML = '<tr><td colspan="12" class="text-center text-danger">Error loading data. Please check your connection and try again.</td></tr>';
    });
}

function renderAdminTable(admins) {
    const tbody = document.getElementById('adminTableBody');
    tbody.innerHTML = '';

    if (admins.length === 0) {
        tbody.innerHTML = '<tr><td colspan="12" class="text-center">No admin users found</td></tr>';
        return;
    }

    admins.forEach(admin => {
        const fullName = ((admin.first_name || '') + ' ' + (admin.last_name || '')).trim() || 'N/A';
        const profileImage = admin.profile_picture
            ? `<img src="<?= base_url('uploads/Admin_Profile/') ?>${admin.profile_picture}" alt="Profile" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">`
            : `<div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-user text-white"></i></div>`;

        const roleBadge = admin.role === 'super_admin'
            ? '<span class="badge badge-danger">Super Admin</span>'
            : admin.role === 'admin'
                ? '<span class="badge badge-primary">Admin</span>'
                : '<span class="badge badge-info">Employee</span>';

        const statusBadge = (admin.active == 1)
            ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Active</span>'
            : '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactive</span>';

        const sms2faBadge = (admin.sms_2fa_enabled == 1)
            ? '<span class="badge badge-success"><i class="fas fa-check"></i> Enabled</span>'
            : '<span class="badge badge-secondary"><i class="fas fa-times"></i> Disabled</span>';

        const createdDate = admin.created_at ? new Date(admin.created_at).toLocaleDateString('en-IN', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }) : 'N/A';

        let actions = `<a href="<?= base_url('admin/admin-users/edit/') ?>${admin.id}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>`;

        <?php if (session()->get('admin_role') === 'super_admin'): ?>
        if (admin.id != <?= session()->get('admin_id') ?>) {
            actions += `<button class="btn btn-sm btn-danger ml-1" onclick="changePassword(${admin.id}, '${admin.username}')" title="Change Password"><i class="fas fa-key"></i></button>`;
        }
        <?php endif; ?>

        const departmentName = admin.department_name || 'N/A';

        const row = `<tr>
            <td>${admin.id}</td>
            <td class="text-center">${profileImage}</td>
            <td>${admin.username}</td>
            <td>${fullName}</td>
            <td>${admin.email}</td>
            <td>${admin.mobile || 'N/A'}</td>
            <td>${departmentName}</td>
            <td>${roleBadge}</td>
            <td class="text-center">${statusBadge}</td>
            <td class="text-center">${sms2faBadge}</td>
            <td>${createdDate}</td>
            <td>${actions}</td>
        </tr>`;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

function applyFilters() {
    loadAdminUsers();
}

function clearFilters() {
    document.getElementById('filterForm').reset();
    loadAdminUsers();
}

function changePassword(userId, userName) {
    document.getElementById('userName').textContent = userName;
    document.getElementById('changePasswordForm').action = '<?= base_url('admin/admin-users/update-password/') ?>' + userId;

    // Use Bootstrap modal without jQuery
    const modal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
    modal.show();
}

// Load initial data and setup auto-hide notifications
document.addEventListener('DOMContentLoaded', function() {
    loadAdminUsers();

    // Auto-hide success notifications
    const alerts = document.querySelectorAll('.alert-success.auto-hide');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 500);
        }, 5000);
    });
});
</script>
<?= $this->endSection() ?>