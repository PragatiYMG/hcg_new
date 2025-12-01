<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Bank Management</h3>
                    <div>
                        <button class="btn btn-outline-secondary btn-sm" type="button" data-toggle="collapse" data-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                            <i class="fas fa-filter"></i> Filters
                        </button>
                        <a href="?export=csv<?= !empty($_GET) ? '&' . http_build_query(array_diff_key($_GET, ['export' => ''])) : '' ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addBankModal">
                            <i class="fas fa-plus"></i> Add Bank
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Success/Error Messages -->
                    <div id="alertContainer"></div>

                    <!-- Filter Form -->
                    <div class="collapse" id="filterCollapse">
                        <div class="card card-body mb-3">
                            <form id="filterForm" class="row g-3">
                                <div class="col-md-2">
                                    <label for="created_from" class="form-label">Created From</label>
                                    <input type="date" name="created_from" id="created_from" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-2">
                                    <label for="created_to" class="form-label">Created To</label>
                                    <input type="date" name="created_to" id="created_to" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-2">
                                    <label for="updated_from" class="form-label">Updated From</label>
                                    <input type="date" name="updated_from" id="updated_from" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-2">
                                    <label for="updated_to" class="form-label">Updated To</label>
                                    <input type="date" name="updated_to" id="updated_to" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-4 d-flex align-items-end justify-content-around">
                                    <button type="submit" class="btn btn-primary btn-sm me-2">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <button type="button" id="clearFilters" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-times"></i> Clear
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="banksTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Bank Name</th>
                                    <th>Added By</th>
                                    <th>Last Update</th>
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

<!-- Add Bank Modal -->
<div class="modal fade" id="addBankModal" tabindex="-1" role="dialog" aria-labelledby="addBankModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBankModalLabel">Add New Bank</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addBankForm">
                <div class="modal-body">
                    <div id="addBankErrors"></div>
                    <div class="form-group">
                        <label for="bank_name">Bank Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="bank_name" name="bank_name" required>
                        <small class="form-text text-muted">Enter the full name of the bank (e.g., State Bank of India, HDFC Bank)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Bank</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Bank Modal -->
<div class="modal fade" id="editBankModal" tabindex="-1" role="dialog" aria-labelledby="editBankModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBankModalLabel">Edit Bank</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editBankForm">
                <input type="hidden" id="edit_bank_id" name="bank_id">
                <div class="modal-body">
                    <div id="editBankErrors"></div>
                    <div class="form-group">
                        <label for="edit_bank_name">Bank Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_bank_name" name="bank_name" required>
                        <small class="form-text text-muted">Enter the full name of the bank (e.g., State Bank of India, HDFC Bank)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Bank</button>
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
    const banksTable = $('#banksTable').DataTable({
        ajax: {
            url: '<?= base_url('admin/banks/getTableData') ?>',
            type: 'GET',
            data: function(d) {
                // Add filter parameters to the request
                d.created_from = $('#created_from').val();
                d.created_to = $('#created_to').val();
                d.updated_from = $('#updated_from').val();
                d.updated_to = $('#updated_to').val();
            }
        },
        columns: [
            { data: 'index', orderable: false },
            { data: 'bank_name', orderable: true },
            { data: 'created_by', orderable: false },
            { data: 'updated_info', orderable: false },
            { data: 'actions', orderable: false }
        ],
        pageLength: 10,
        lengthChange: true,
        ordering: true,
        searching: true,
        autoWidth: false,
        responsive: true,
        language: {
            emptyTable: "No banks found."
        }
    });

    // Filter form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        banksTable.ajax.reload();
    });

    // Clear filters
    $('#clearFilters').on('click', function() {
        $('#created_from').val('');
        $('#created_to').val('');
        $('#updated_from').val('');
        $('#updated_to').val('');
        banksTable.ajax.reload();
    });

    // Add Bank Form Submit
    $('#addBankForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            url: '<?= base_url('admin/banks/store') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#addBankModal').modal('hide');
                    $('#addBankForm')[0].reset();
                    showAlert('success', response.message);
                    // Reload table data without page refresh
                    banksTable.ajax.reload(null, false);
                } else {
                    displayErrors('#addBankErrors', response.errors);
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while adding the bank.');
            }
        });
    });

    // Edit Bank Form Submit
    $('#editBankForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const bankId = $('#edit_bank_id').val();

        $.ajax({
            url: '<?= base_url('admin/banks/update/') ?>' + bankId,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#editBankModal').modal('hide');
                    showAlert('success', response.message);
                    // Reload table data without page refresh
                    banksTable.ajax.reload(null, false);
                } else {
                    displayErrors('#editBankErrors', response.errors);
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while updating the bank.');
            }
        });
    });

    // Reset forms when modals are closed
    $('#addBankModal').on('hidden.bs.modal', function() {
        $('#addBankForm')[0].reset();
        $('#addBankErrors').html('');
    });

    $('#editBankModal').on('hidden.bs.modal', function() {
        $('#editBankForm')[0].reset();
        $('#editBankErrors').html('');
    });
});

function editBank(id) {
    // Get bank data from server
    $.ajax({
        url: '<?= base_url('admin/banks/getBank/') ?>' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                $('#edit_bank_id').val(response.data.id);
                $('#edit_bank_name').val(response.data.bank_name);
                $('#editBankModal').modal('show');
            } else {
                showAlert('error', response.message || 'Failed to load bank data.');
            }
        },
        error: function() {
            showAlert('error', 'Failed to load bank data.');
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
</script>
<?= $this->endSection() ?>