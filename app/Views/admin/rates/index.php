<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Rate Management</h3>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addRateModal">
                        <i class="fas fa-plus"></i> Add Rate
                    </button>
                </div>
                <div class="card-body">
                    <!-- Success/Error Messages -->
                    <div id="alertContainer"></div>

                    <!-- Rates Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="ratesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Basic Rate</th>
                                    <th>Full Rate</th>
                                    <th>Effective Date</th>
                                    <th>Status</th>
                                    <th>Added By</th>
                                    <th>Last Update</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="ratesTableBody">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Rate Modal -->
<div class="modal fade" id="addRateModal" tabindex="-1" role="dialog" aria-labelledby="addRateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRateModalLabel">Add New Rate</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addRateForm">
                <div class="modal-body">
                    <div id="addRateErrors"></div>
                    <div class="form-group">
                        <label for="basic_rate">Basic Rate <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="basic_rate" name="basic_rate" step="0.001" min="0" required>
                        <small class="form-text text-muted">Enter the base rate amount (will calculate full rate)</small>
                    </div>
                    <div class="form-group">
                        <label for="full_rate">Full Rate <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="full_rate" name="full_rate" step="0.01" min="0" required>
                        <small class="form-text text-muted">Enter full rate amount (will calculate basic rate)</small>
                    </div>
                    <div class="form-group">
                        <label for="effective_date">Effective Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="effective_date" name="effective_date" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <small class="form-text text-muted">⚠️ Only one rate can be active at a time</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Rate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Rate Modal -->
<div class="modal fade" id="editRateModal" tabindex="-1" role="dialog" aria-labelledby="editRateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRateModalLabel">Edit Rate</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editRateForm">
                <input type="hidden" id="edit_rate_id" name="rate_id">
                <div class="modal-body">
                    <div id="editRateErrors"></div>
                    <div id="cautionMessage" class="alert alert-warning" style="display: none;"></div>
                    <div class="form-group">
                        <label for="edit_basic_rate">Basic Rate <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_basic_rate" name="basic_rate" step="0.001" min="0" required>
                        <small class="form-text text-muted">Enter the base rate amount (will calculate full rate)</small>
                    </div>
                    <div class="form-group">
                        <label for="edit_full_rate">Full Rate <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_full_rate" name="full_rate" step="0.01" min="0" required>
                        <small class="form-text text-muted">Enter full rate amount (will calculate basic rate)</small>
                    </div>
                    <div class="form-group">
                        <label for="edit_effective_date">Effective Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="edit_effective_date" name="effective_date" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_status">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <small class="form-text text-muted">⚠️ Only one rate can be active at a time</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Rate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let taxRates = {};

$(document).ready(function() {
    loadTaxRates();
    loadRates();

    // Bidirectional calculation for add form
    $('#basic_rate').on('input', function() {
        calculateFromBasic('basic_rate', 'full_rate');
    });

    $('#full_rate').on('input', function() {
        calculateFromFull('full_rate', 'basic_rate');
    });

    // Bidirectional calculation for edit form
    $('#edit_basic_rate').on('input', function() {
        calculateFromBasic('edit_basic_rate', 'edit_full_rate');
    });

    $('#edit_full_rate').on('input', function() {
        calculateFromFull('edit_full_rate', 'edit_basic_rate');
    });

    // Add Rate Form Submit
    $('#addRateForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            url: '<?= base_url('admin/rates/store') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#addRateModal').modal('hide');
                    $('#addRateForm')[0].reset();
                    showAlert('success', response.message);
                    loadRates();
                } else {
                    displayErrors('#addRateErrors', response.errors);
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while adding the rate.');
            }
        });
    });

    // Edit Rate Form Submit
    $('#editRateForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const rateId = $('#edit_rate_id').val();

        $.ajax({
            url: '<?= base_url('admin/rates/update/') ?>' + rateId,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#editRateModal').modal('hide');
                    showAlert('success', response.message);
                    if (response.caution) {
                        showAlert('warning', response.caution);
                    }
                    loadRates();
                } else {
                    displayErrors('#editRateErrors', response.errors);
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while updating the rate.');
            }
        });
    });

    // Reset forms when modals are closed
    $('#addRateModal').on('hidden.bs.modal', function() {
        $('#addRateForm')[0].reset();
        $('#addRateErrors').html('');
    });

    $('#editRateModal').on('hidden.bs.modal', function() {
        $('#editRateForm')[0].reset();
        $('#editRateErrors').html('');
        $('#cautionMessage').hide();
    });
});

function loadTaxRates() {
    $.ajax({
        url: '<?= base_url('admin/rates/getTaxRates') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                taxRates = response.data;
                console.log('Tax rates loaded:', taxRates);
            } else {
                console.error('Failed to load tax rates');
            }
        },
        error: function() {
            console.error('Error loading tax rates');
        }
    });
}

function calculateFromBasic(basicInputId, fullInputId) {
    const basicRate = parseFloat($('#' + basicInputId).val());
    if (!isNaN(basicRate) && basicRate > 0 && Object.keys(taxRates).length > 0) {
        // Formula: Full Rate = Basic rate + (basic rate * VAT) + ((Basic Rate*VAT)*Surcharge)
        const vat = taxRates.VAT || taxRates.vat || 0;
        const surcharge = taxRates.Surcharge || taxRates.surcharge || 0;

        const vatAmount = basicRate * (vat / 100);
        const surchargeAmount = vatAmount * (surcharge / 100);
        const fullRate = basicRate + vatAmount + surchargeAmount;

        $('#' + fullInputId).val(fullRate.toFixed(2));
    }
}

function calculateFromFull(fullInputId, basicInputId) {
    const fullRate = parseFloat($('#' + fullInputId).val());
    if (!isNaN(fullRate) && fullRate > 0 && Object.keys(taxRates).length > 0) {
        // Reverse calculation using algebraic formula
        const vat = taxRates.VAT || taxRates.vat || 0;
        const surcharge = taxRates.Surcharge || taxRates.surcharge || 0;

        const v = vat / 100; // VAT percentage as decimal
        const s = surcharge / 100; // Surcharge percentage as decimal

        // Formula: X = Full Rate / (1 + v + v*s)
        const denominator = 1 + v + (v * s);
        const basicRate = fullRate / denominator;

        $('#' + basicInputId).val(basicRate.toFixed(3));
    }
}

function loadRates() {
    $.ajax({
        url: '<?= base_url('admin/rates/getRates') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                displayRates(response.data);
            }
        },
        error: function() {
            showAlert('error', 'Failed to load rates.');
        }
    });
}

function displayRates(rates) {
    let html = '';
    if (rates.length === 0) {
        html = '<tr><td colspan="8" class="text-center">No rates found.</td></tr>';
    } else {
        rates.forEach(function(rate) {
            html += `
                <tr>
                    <td>${rate.id}</td>
                    <td><strong>₹${parseFloat(rate.basic_rate).toFixed(2)}</strong></td>
                    <td><strong>₹${parseFloat(rate.full_rate).toFixed(2)}</strong></td>
                    <td>${new Date(rate.effective_date).toLocaleDateString()}</td>
                    <td><span class="badge badge-${rate.status === 'active' ? 'success' : 'secondary'}">${rate.status.charAt(0).toUpperCase() + rate.status.slice(1)}</span></td>
                    <td>
                        <small>
                            <strong>${escapeHtml(rate.created_by_name)}</strong><br>
                            ${new Date(rate.created_date).toLocaleDateString()}
                        </small>
                    </td>
                    <td>
                        ${rate.updated_at ? `<small><strong>${escapeHtml(rate.updated_by_name || 'Unknown')}</strong><br>${new Date(rate.updated_at).toLocaleDateString()}</small>` : '<small class="text-muted">Never updated</small>'}
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editRate(${rate.id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    $('#ratesTableBody').html(html);
}

function editRate(id) {
    $.ajax({
        url: '<?= base_url('admin/rates/getRates') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const rate = response.data.find(r => r.id == id);
                if (rate) {
                    $('#edit_rate_id').val(rate.id);
                    $('#edit_basic_rate').val(rate.basic_rate);
                    $('#edit_full_rate').val(rate.full_rate);
                    $('#edit_effective_date').val(rate.effective_date);
                    $('#edit_status').val(rate.status);
                    $('#editRateModal').modal('show');
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