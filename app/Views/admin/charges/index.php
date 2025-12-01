<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Charge Management</h3>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addChargeModal">
                        <i class="fas fa-plus"></i> Add Charge
                    </button>
                </div>
                <div class="card-body">
                    <!-- Success/Error Messages -->
                    <div id="alertContainer"></div>

                    <!-- Charges Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="chargesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Charge Name</th>
                                    <th>Charge Value</th>
                                    <th>Added By</th>
                                    <th>Last Update</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="chargesTableBody">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Charge Modal -->
<div class="modal fade" id="addChargeModal" tabindex="-1" role="dialog" aria-labelledby="addChargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addChargeModalLabel">Add New Charge</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addChargeForm">
                <div class="modal-body">
                    <div id="addChargeErrors"></div>
                    <div class="form-group">
                        <label for="charge_name">Charge Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="charge_name" name="charge_name" required>
                        <small class="form-text text-muted">e.g., Late Charge, Bounce Charge, Annual Fee</small>
                    </div>
                    <div class="form-group">
                        <label for="charge_value">Charge Value (₹) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="charge_value" name="charge_value" step="0.01" min="0" required>
                        <small class="form-text text-muted">Enter the charge amount in rupees</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Charge</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Charge Modal -->
<div class="modal fade" id="editChargeModal" tabindex="-1" role="dialog" aria-labelledby="editChargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editChargeModalLabel">Edit Charge</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editChargeForm">
                <input type="hidden" id="edit_charge_id" name="charge_id">
                <div class="modal-body">
                    <div id="editChargeErrors"></div>
                    <div id="cautionMessage" class="alert alert-warning" style="display: none;"></div>
                    <div class="form-group">
                        <label for="edit_charge_name">Charge Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_charge_name" name="charge_name" required>
                        <small class="form-text text-muted">e.g., Late Charge, Bounce Charge, Annual Fee</small>
                    </div>
                    <div class="form-group">
                        <label for="edit_charge_value">Charge Value (₹) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_charge_value" name="charge_value" step="0.01" min="0" required>
                        <small class="form-text text-muted">Enter the charge amount in rupees</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Charge</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    loadCharges();

    // Add Charge Form Submit
    $('#addChargeForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            url: '<?= base_url('admin/charges/store') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#addChargeModal').modal('hide');
                    $('#addChargeForm')[0].reset();
                    showAlert('success', response.message);
                    loadCharges();
                } else {
                    displayErrors('#addChargeErrors', response.errors);
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while adding the charge.');
            }
        });
    });

    // Edit Charge Form Submit
    $('#editChargeForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const chargeId = $('#edit_charge_id').val();

        $.ajax({
            url: '<?= base_url('admin/charges/update/') ?>' + chargeId,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#editChargeModal').modal('hide');
                    showAlert('success', response.message);
                    if (response.caution) {
                        showAlert('warning', response.caution);
                    }
                    loadCharges();
                } else {
                    displayErrors('#editChargeErrors', response.errors);
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while updating the charge.');
            }
        });
    });

    // Reset forms when modals are closed
    $('#addChargeModal').on('hidden.bs.modal', function() {
        $('#addChargeForm')[0].reset();
        $('#addChargeErrors').html('');
    });

    $('#editChargeModal').on('hidden.bs.modal', function() {
        $('#editChargeForm')[0].reset();
        $('#editChargeErrors').html('');
        $('#cautionMessage').hide();
    });
});

function loadCharges() {
    $.ajax({
        url: '<?= base_url('admin/charges/getCharges') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                displayCharges(response.data);
            }
        },
        error: function() {
            showAlert('error', 'Failed to load charges.');
        }
    });
}

function displayCharges(charges) {
    let html = '';
    if (charges.length === 0) {
        html = '<tr><td colspan="6" class="text-center">No charges found.</td></tr>';
    } else {
        charges.forEach(function(charge) {
            html += `
                <tr>
                    <td>${charge.id}</td>
                    <td><strong>${escapeHtml(charge.charge_name)}</strong></td>
                    <td><strong>₹${parseFloat(charge.charge_value).toFixed(2)}</strong></td>
                    <td>
                        <small>
                            <strong>${escapeHtml(charge.created_by_name)}</strong><br>
                            ${new Date(charge.created_date).toLocaleDateString()}
                        </small>
                    </td>
                    <td>
                        ${charge.updated_date ? `<small><strong>${escapeHtml(charge.updated_by_name || 'Unknown')}</strong><br>${new Date(charge.updated_date).toLocaleDateString()}</small>` : '<small class="text-muted">Never updated</small>'}
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editCharge(${charge.id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    $('#chargesTableBody').html(html);
}

function editCharge(id) {
    $.ajax({
        url: '<?= base_url('admin/charges/getCharges') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const charge = response.data.find(c => c.id == id);
                if (charge) {
                    $('#edit_charge_id').val(charge.id);
                    $('#edit_charge_name').val(charge.charge_name);
                    $('#edit_charge_value').val(charge.charge_value);
                    $('#editChargeModal').modal('show');
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