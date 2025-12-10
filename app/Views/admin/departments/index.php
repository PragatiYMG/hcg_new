<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Department Management</h3>
                    <div>
                        <button class="btn btn-outline-secondary btn-sm" type="button" data-toggle="collapse" data-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                            <i class="fas fa-filter"></i> Filters
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addDepartmentModal">
                            <i class="fas fa-plus"></i> Add Department
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
                        <table class="table table-striped table-bordered" id="departmentsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Department Name</th>
                                    <th>Status</th>
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

<!-- Add Department Modal -->
<div class="modal fade" id="addDepartmentModal" tabindex="-1" role="dialog" aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDepartmentModalLabel">Add New Department</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addDepartmentForm">
                <div class="modal-body">
                    <div id="addDepartmentErrors"></div>
                    <div class="form-group">
                        <label for="department_name">Department Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="department_name" name="department_name" required>
                        <small class="form-text text-muted">Enter the department name (e.g., Human Resources, IT, Finance)</small>
                    </div>
                    <div class="form-group">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Department</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Department Modal -->
<div class="modal fade" id="editDepartmentModal" tabindex="-1" role="dialog" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDepartmentModalLabel">Edit Department</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editDepartmentForm">
                <input type="hidden" id="edit_department_id" name="department_id">
                <div class="modal-body">
                    <div id="editDepartmentErrors"></div>
                    <div class="form-group">
                        <label for="edit_department_name">Department Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_department_name" name="department_name" required>
                        <small class="form-text text-muted">Enter the department name (e.g., Human Resources, IT, Finance)</small>
                    </div>
                    <div class="form-group">
                        <label for="edit_status">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Department</button>
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
    const departmentsTable = $('#departmentsTable').DataTable({
        ajax: {
            url: '<?= base_url('admin/departments/getTableData') ?>',
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
            { data: 'department_name', orderable: true },
            { data: 'status', orderable: false },
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
            emptyTable: "No departments found."
        }
    });

    // Filter form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        departmentsTable.ajax.reload();
    });

    // Clear filters
    $('#clearFilters').on('click', function() {
        $('#created_from').val('');
        $('#created_to').val('');
        $('#updated_from').val('');
        $('#updated_to').val('');
        departmentsTable.ajax.reload();
    });

    // Add Department Form Submit
    $('#addDepartmentForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            url: '<?= base_url('admin/departments/store') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#addDepartmentModal').modal('hide');
                    $('#addDepartmentForm')[0].reset();
                    showAlert('success', response.message);
                    // Reload table data without page refresh
                    departmentsTable.ajax.reload(null, false);
                } else {
                    displayErrors('#addDepartmentErrors', response.errors);
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while adding the department.');
            }
        });
    });

    // Edit Department Form Submit
    $('#editDepartmentForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const departmentId = $('#edit_department_id').val();

        $.ajax({
            url: '<?= base_url('admin/departments/update/') ?>' + departmentId,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#editDepartmentModal').modal('hide');
                    showAlert('success', response.message);
                    // Reload table data without page refresh
                    departmentsTable.ajax.reload(null, false);
                } else {
                    displayErrors('#editDepartmentErrors', response.errors);
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while updating the department.');
            }
        });
    });

    // Reset forms when modals are closed
    $('#addDepartmentModal').on('hidden.bs.modal', function() {
        $('#addDepartmentForm')[0].reset();
        $('#addDepartmentErrors').html('');
    });

    $('#editDepartmentModal').on('hidden.bs.modal', function() {
        $('#editDepartmentForm')[0].reset();
        $('#editDepartmentErrors').html('');
    });
});

function editDepartment(id) {
    // Get department data from server
    $.ajax({
        url: '<?= base_url('admin/departments/getDepartment/') ?>' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                $('#edit_department_id').val(response.data.id);
                $('#edit_department_name').val(response.data.department_name);
                $('#edit_status').val(response.data.status);
                $('#editDepartmentModal').modal('show');
            } else {
                showAlert('error', response.message || 'Failed to load department data.');
            }
        },
        error: function() {
            showAlert('error', 'Failed to load department data.');
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