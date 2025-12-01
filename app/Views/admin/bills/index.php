<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Bill Management</h3>
                    <div>
                        <a href="<?= base_url('admin/bills/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Create New Bill
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Success/Error Messages -->
                    <div id="alertContainer"></div>

                    <!-- Info Alert -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Important:</strong> Only one bill setting can be active at a time. Older invoices will use the bill version that was active on their creation date.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="billsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Version</th>
                                    <th>Company Name</th>
                                    <th>Status</th>
                                    <th>Effective Date</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activate Bill Modal -->
<div class="modal fade" id="activateBillModal" tabindex="-1" role="dialog" aria-labelledby="activateBillModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="activateBillModalLabel">Activate Bill Version</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="activateBillForm">
                <input type="hidden" id="activate_bill_id">
                <div class="modal-body">
                    <div id="activateBillErrors"></div>
                    <div class="alert alert-warning">
                        <strong>Warning:</strong> Activating this bill will deactivate all other active bills. This action cannot be undone.
                    </div>
                    <div class="form-group">
                        <label for="effective_date">Effective Date (Optional)</label>
                        <input type="date" class="form-control" id="effective_date" name="effective_date">
                        <small class="form-text text-muted">Leave empty to activate immediately</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Activate Bill
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Initialize DataTable with AJAX
    const billsTable = $('#billsTable').DataTable({
        ajax: {
            url: '<?= base_url('admin/bills/getTableData') ?>',
            type: 'GET'
        },
        columns: [
            { data: 'index', orderable: false },
            { data: 'version', orderable: true },
            { data: 'company_name', orderable: true },
            { data: 'status', orderable: false },
            { data: 'effective_date', orderable: true },
            { data: 'created_by', orderable: false },
            { data: 'actions', orderable: false }
        ],
        pageLength: 10,
        lengthChange: true,
        ordering: true,
        searching: true,
        autoWidth: false,
        responsive: true,
        order: [[ 1, 'desc' ]], // Sort by version descending
        language: {
            emptyTable: "No bills found."
        }
    });

    // Activate Bill Form Submit
    $('#activateBillForm').on('submit', function(e) {
        e.preventDefault();
        const billId = $('#activate_bill_id').val();
        const formData = new FormData(this);

        $.ajax({
            url: '<?= base_url('admin/bills/activate/') ?>' + billId,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#activateBillModal').modal('hide');
                    showAlert('success', response.message);
                    billsTable.ajax.reload(null, false);
                } else {
                    displayErrors('#activateBillErrors', response.errors);
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while activating the bill.');
            }
        });
    });

    // Reset forms when modals are closed
    $('#activateBillModal').on('hidden.bs.modal', function() {
        $('#activateBillForm')[0].reset();
        $('#activateBillErrors').html('');
    });
});

function activateBill(id) {
    $('#activate_bill_id').val(id);
    $('#activateBillModal').modal('show');
}

function duplicateBill(id) {
    if (confirm('Are you sure you want to duplicate this bill as a draft?')) {
        $.ajax({
            url: '<?= base_url('admin/bills/duplicate/') ?>' + id,
            type: 'POST',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#billsTable').DataTable().ajax.reload(null, false);
                } else {
                    showAlert('error', response.message || 'Failed to duplicate bill');
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while duplicating the bill.');
            }
        });
    }
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
</script>
<?= $this->endSection() ?>