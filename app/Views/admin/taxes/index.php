<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Tax Management</h3>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addTaxModal">
                        <i class="fas fa-plus"></i> Add Tax
                    </button>
                </div>
                <div class="card-body">
                    <!-- Success/Error Messages -->
                    <div id="alertContainer"></div>

                    <!-- Taxes Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="taxesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tax Name</th>
                                    <th>Tax Rate (%)</th>
                                    <th>Status</th>
                                    <th>Online Status</th>
                                    <th>Created By</th>
                                    <th>Last Update</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="taxesTableBody">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Tax Modal -->
<div class="modal fade" id="addTaxModal" tabindex="-1" role="dialog" aria-labelledby="addTaxModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTaxModalLabel">Add New Tax</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addTaxForm">
                <div class="modal-body">
                    <div id="addTaxErrors"></div>
                    <div class="form-group">
                        <label for="type_name">Tax Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="type_name" name="type_name" required>
                        <small class="form-text text-muted">e.g., VAT, GST, Service Tax</small>
                    </div>
                    <div class="form-group">
                        <label for="tax_rate">Tax Rate (%) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="tax_rate" name="tax_rate" step="0.01" min="0" max="100" required>
                        <small class="form-text text-muted">Enter rate as percentage (e.g., 5 for 5%)</small>
                    </div>
                    <div class="form-group">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="online_status">Online Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="online_status" name="online_status" required>
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Tax</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Tax Modal -->
<div class="modal fade" id="editTaxModal" tabindex="-1" role="dialog" aria-labelledby="editTaxModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTaxModalLabel">Edit Tax</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editTaxForm">
                <input type="hidden" id="edit_tax_id" name="tax_id">
                <div class="modal-body">
                    <div id="editTaxErrors"></div>
                    <div id="cautionMessage" class="alert alert-warning" style="display: none;"></div>
                    <div class="form-group">
                        <label for="edit_type_name">Tax Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_type_name" name="type_name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_tax_rate">Tax Rate (%) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_tax_rate" name="tax_rate" step="0.01" min="0" max="100" required>
                        <small class="form-text text-muted">⚠️ Changing tax rate will affect billing calculations</small>
                    </div>
                    <div class="form-group">
                        <label for="edit_status">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_online_status">Online Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_online_status" name="online_status" required>
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Tax</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    loadTaxes();

    // Add Tax Form Submit
    $('#addTaxForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            url: '<?= base_url('admin/taxes/store') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#addTaxModal').modal('hide');
                    $('#addTaxForm')[0].reset();
                    showAlert('success', response.message);
                    loadTaxes();
                } else {
                    displayErrors('#addTaxErrors', response.errors);
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while adding the tax.');
            }
        });
    });

    // Edit Tax Form Submit
    $('#editTaxForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const taxId = $('#edit_tax_id').val();

        $.ajax({
            url: '<?= base_url('admin/taxes/update/') ?>' + taxId,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#editTaxModal').modal('hide');
                    showAlert('success', response.message);
                    if (response.caution) {
                        showAlert('warning', response.caution);
                    }
                    loadTaxes();
                } else {
                    displayErrors('#editTaxErrors', response.errors);
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while updating the tax.');
            }
        });
    });

    // Reset forms when modals are closed
    $('#addTaxModal').on('hidden.bs.modal', function() {
        $('#addTaxForm')[0].reset();
        $('#addTaxErrors').html('');
    });

    $('#editTaxModal').on('hidden.bs.modal', function() {
        $('#editTaxForm')[0].reset();
        $('#editTaxErrors').html('');
        $('#cautionMessage').hide();
    });
});

function loadTaxes() {
    $.ajax({
        url: '<?= base_url('admin/taxes/getTaxes') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                displayTaxes(response.data);
            }
        },
        error: function() {
            showAlert('error', 'Failed to load taxes.');
        }
    });
}

function displayTaxes(taxes) {
    let html = '';
    if (taxes.length === 0) {
        html = '<tr><td colspan="8" class="text-center">No taxes found.</td></tr>';
    } else {
        taxes.forEach(function(tax) {
            html += `
                <tr>
                    <td>${tax.id}</td>
                    <td>${escapeHtml(tax.type_name)}</td>
                    <td><strong>${parseFloat(tax.tax_rate).toFixed(2)}%</strong></td>
                    <td><span class="badge badge-${tax.status === 'active' ? 'success' : 'secondary'}">${tax.status.charAt(0).toUpperCase() + tax.status.slice(1)}</span></td>
                    <td><span class="badge badge-${tax.online_status === 'online' ? 'info' : 'secondary'}">${tax.online_status.charAt(0).toUpperCase() + tax.online_status.slice(1)}</span></td>
                    <td>
                        <small>
                            <strong>${escapeHtml(tax.created_by_name)}</strong><br>
                            ${new Date(tax.created_date).toLocaleDateString()}
                        </small>
                    </td>
                    <td>
                        ${tax.updated_at ? `<small><strong>${escapeHtml(tax.updated_by_name || 'Unknown')}</strong><br>${new Date(tax.updated_at).toLocaleDateString()}</small>` : '<small class="text-muted">Never updated</small>'}
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editTax(${tax.id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    $('#taxesTableBody').html(html);
}

function editTax(id) {
    // Find tax data (you might want to store it in a global variable or fetch again)
    $.ajax({
        url: '<?= base_url('admin/taxes/getTaxes') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const tax = response.data.find(t => t.id == id);
                if (tax) {
                    $('#edit_tax_id').val(tax.id);
                    $('#edit_type_name').val(tax.type_name);
                    $('#edit_tax_rate').val(tax.tax_rate);
                    $('#edit_status').val(tax.status);
                    $('#edit_online_status').val(tax.online_status);
                    $('#editTaxModal').modal('show');
                }
            }
        }
    });
}

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : (type === 'warning' ? 'alert-warning' : 'alert-danger');
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <strong>${type.charAt(0).toUpperCase() + type.slice(1)}!</strong> ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    $('#alertContainer').html(alertHtml);

    // Auto-hide success alerts after 5 seconds
    if (type === 'success') {
        setTimeout(() => {
            $('.alert').fadeOut();
        }, 5000);
    }
}

function displayErrors(container, errors) {
    let html = '<div class="alert alert-danger"><ul class="mb-0">';
    for (const [field, error] of Object.entries(errors)) {
        html += `<li>${error}</li>`;
    }
    html += '</ul></div>';
    $(container).html(html);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<style>
.table th {
    vertical-align: middle;
}
.table td {
    vertical-align: middle;
}
.badge {
    font-size: 0.8em;
}
</style>
<?= $this->endSection() ?>
