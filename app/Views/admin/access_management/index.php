<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>Access Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Access Management</h4>
                    <small class="text-muted">Manage permissions for individual users</small>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> Permissions are managed individually for each user. Super admin users automatically have full access to all features.
                    </div>

                    <!-- User Permissions Section -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="list-group" id="userList">
                                <?php foreach ($users as $user): ?>
                                    <a href="#" class="list-group-item list-group-item-action user-item"
                                       data-user-id="<?= $user['id'] ?>">
                                        <strong><?= esc(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: $user['username']) ?></strong>
                                        <br><small class="text-muted">
                                            Role: <?= ucfirst(str_replace('_', ' ', $user['role'])) ?>
                                        </small>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="mb-3">
                                <button type="button" class="btn btn-sm btn-outline-info" onclick="showCopyPermissionsModal()" id="copyPermissionsBtn" style="display: none;">
                                    <i class="fas fa-copy"></i> Copy Permissions from Another Admin
                                </button>
                            </div>
                            <div id="userPermissionsContainer">
                                <div class="text-center">
                                    <p class="text-muted">Select a user to manage permissions</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Copy Permissions Modal -->
<div class="modal fade" id="copyPermissionsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Copy Permissions from Another Admin</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="sourceUserSelect">Select Admin to Copy From:</label>
                    <select class="form-control" id="sourceUserSelect">
                        <option value="">Choose an admin...</option>
                        <?php foreach ($users as $user): ?>
                            <?php if ($user['id'] != session()->get('admin_id')): // Don't show current user ?>
                                <option value="<?= $user['id'] ?>"><?= esc(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: $user['username']) ?> (<?= ucfirst(str_replace('_', ' ', $user['role'])) ?>)</option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> This will replace all current permissions for the selected user with the permissions from the chosen admin.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="copyPermissionsFromUser()">Copy Permissions</button>
            </div>
        </div>
    </div>
</div>

<!-- Permission Modal Template -->
<div id="permissionModalTemplate" style="display: none;">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0" id="modalTitle">Manage Permissions</h5>
            <div>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="selectAllBtn" onclick="toggleSelectAllPermissions()">
                    Select All
                </button>
            </div>
        </div>
        <div class="card-body" style="max-height: 500px; overflow-y: auto;">
            <form id="permissionForm">
                <div id="permissionsList">
                    <!-- Permissions will be loaded here -->
                </div>
            </form>
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-secondary" onclick="cancelPermissions()">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="savePermissions()">Save Changes</button>
        </div>
    </div>
</div>

<?= $this->section('page-scripts') ?>
<script>
let currentUserId = null;
let currentPermissions = {};

$(document).ready(function() {
    // User selection
    $('.user-item').on('click', function(e) {
        e.preventDefault();
        $('.user-item').removeClass('active');
        $(this).addClass('active');
        currentUserId = $(this).data('user-id');
        $('#copyPermissionsBtn').show();
        loadUserPermissions(currentUserId);
    });
});

function loadUserPermissions(userId) {
    $.get(`<?= base_url('admin/access-management/get-user-permissions') ?>?user_id=${userId}`)
        .done(function(response) {
            if (response.permissions) {
                currentPermissions = {};
                response.permissions.forEach(perm => {
                    currentPermissions[perm.id] = perm.granted;
                });
                showPermissionModal('user', userId);
            }
        })
        .fail(function() {
            toastr.error('Failed to load user permissions');
        });
}

function showPermissionModal(type, target) {
    const container = '#userPermissionsContainer';
    const template = $('#permissionModalTemplate').html();

    $(container).html(template);

    // Load permissions grouped by module
    const permissions = <?= json_encode($permissions ?? []) ?>;
    let html = '';

    if (Object.keys(permissions).length === 0) {
        html = '<div class="alert alert-warning">No permissions found. Please run the database seeders.</div>';
    } else {
        Object.keys(permissions).forEach(module => {
            html += `
                <div class="permission-module mb-4">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-folder"></i> ${module.charAt(0).toUpperCase() + module.slice(1)}
                    </h6>
                    <div class="row">
            `;

            permissions[module].forEach(permission => {
                const isChecked = currentPermissions[permission.id] || false;
                const checkboxId = `perm_${permission.id}`;

                html += `
                    <div class="col-md-6 mb-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox"
                                   class="custom-control-input permission-checkbox"
                                   id="${checkboxId}"
                                   data-permission-id="${permission.id}"
                                   ${isChecked ? 'checked' : ''}>
                            <label class="custom-control-label" for="${checkboxId}">
                                <strong>${permission.display_name}</strong>
                                ${permission.description ? `<br><small class="text-muted">${permission.description}</small>` : ''}
                            </label>
                        </div>
                    </div>
                `;
            });

            html += `
                    </div>
                </div>
            `;
        });
    }

    $('#permissionsList').html(html);

    $('#modalTitle').text(`Manage Permissions for User #${target}`);

    // Update select all button text and add change listeners
    updateSelectAllButtonText();
    $('.permission-checkbox').on('change', updateSelectAllButtonText);
}

function toggleSelectAllPermissions() {
    const allChecked = $('.permission-checkbox:checked').length === $('.permission-checkbox').length;
    const newState = !allChecked;

    $('.permission-checkbox').prop('checked', newState);
    updateSelectAllButtonText();
}

function updateSelectAllButtonText() {
    const allChecked = $('.permission-checkbox:checked').length === $('.permission-checkbox').length;
    const someChecked = $('.permission-checkbox:checked').length > 0;

    const btn = $('#selectAllBtn');
    if (allChecked) {
        btn.html('<i class="fas fa-minus-square"></i> Unselect All');
        btn.removeClass('btn-outline-secondary').addClass('btn-outline-danger');
    } else if (someChecked) {
        btn.html('<i class="fas fa-check-square"></i> Select All');
        btn.removeClass('btn-outline-danger').addClass('btn-outline-secondary');
    } else {
        btn.html('<i class="fas fa-check-square"></i> Select All');
        btn.removeClass('btn-outline-danger').addClass('btn-outline-secondary');
    }
}

function cancelPermissions() {
    const container = '#userPermissionsContainer';
    $(container).html('<div class="text-center"><p class="text-muted">Select a user to manage permissions</p></div>');
    currentUserId = null;
    currentPermissions = {};
    $('#copyPermissionsBtn').hide();
}

function showCopyPermissionsModal() {
    if (!currentUserId) {
        toastr.error('Please select a user first');
        return;
    }
    $('#copyPermissionsModal').modal('show');
}

function copyPermissionsFromUser() {
    const sourceUserId = $('#sourceUserSelect').val();

    if (!sourceUserId) {
        toastr.error('Please select an admin to copy permissions from');
        return;
    }

    if (sourceUserId == currentUserId) {
        toastr.error('Cannot copy permissions from the same user');
        return;
    }

    // Get source user's permissions
    $.get(`<?= base_url('admin/access-management/get-user-permissions') ?>?user_id=${sourceUserId}`)
        .done(function(response) {
            if (response.permissions) {
                // Apply permissions to current user
                const permissions = response.permissions.map(perm => ({ id: perm.id, granted: true }));

                $.post('<?= base_url('admin/access-management/update-user-permissions') ?>', {
                    user_id: currentUserId,
                    permissions: permissions
                })
                .done(function(updateResponse) {
                    if (updateResponse.success) {
                        toastr.success('Permissions copied successfully');
                        $('#copyPermissionsModal').modal('hide');
                        $('#sourceUserSelect').val('');
                        // Reload current user's permissions
                        loadUserPermissions(currentUserId);
                    } else {
                        toastr.error(updateResponse.message || 'Failed to copy permissions');
                    }
                })
                .fail(function() {
                    toastr.error('Failed to copy permissions');
                });
            }
        })
        .fail(function() {
            toastr.error('Failed to get source user permissions');
        });
}

function savePermissions() {
    const selectedPermissions = [];
    $('.permission-checkbox:checked').each(function() {
        selectedPermissions.push($(this).data('permission-id'));
    });

    const url = '<?= base_url('admin/access-management/update-user-permissions') ?>';
    const data = {
        user_id: currentUserId,
        permissions: selectedPermissions.map(id => ({ id: id, granted: true }))
    };

    $.post(url, data)
        .done(function(response) {
            if (response.success) {
                toastr.success(response.message);
                cancelPermissions();
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('Failed to save permissions');
        });
}
</script>
<?= $this->endSection() ?>

<style>
.permission-module {
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 1rem;
    background-color: #f8f9fa;
}

.list-group-item.active {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.list-group-item.active .text-muted {
    color: rgba(255, 255, 255, 0.7) !important;
}
</style>
<?= $this->endSection() ?>