<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header row">
                    <h3 class="card-title col-6">Connection Statuses Management</h3>
                    <div class="card-tools col-6 text-right">
                        <button type="button" class="btn btn-primary btn-sm" onclick="createStatus()">
                            <i class="fas fa-plus"></i> Add New Status
                        </button>
                    </div>
                </div>
                <div class="card-header">
                    <small class="text-muted">Note: Status names can be edited, new statuses can be added with custom order. Deletion is not allowed to maintain workflow integrity.</small>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped datatable" data-skip-auto-init="true">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Status Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Status Modal -->
<div class="modal fade" id="createStatusModal" tabindex="-1" role="dialog" aria-labelledby="createStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createStatusModalLabel">Add New Connection Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createStatusForm">
                <div class="modal-body">
                    <div id="createFormErrors" class="alert alert-danger" style="display: none;"></div>

                    <div class="form-group">
                        <label for="create_status_name">Status Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="create_status_name" name="status_name" required>
                        <small class="form-text text-muted">Enter a descriptive name for the new status</small>
                    </div>

                    <div class="form-group">
                        <label for="create_status_order">Status Order <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="create_status_order" name="status_order" min="1" required>
                        <small class="form-text text-muted">Order determines the sequence in the workflow (must be unique)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Status Modal -->
<div class="modal fade" id="editStatusModal" tabindex="-1" role="dialog" aria-labelledby="editStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStatusModalLabel">Edit Connection Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editStatusForm">
                <div class="modal-body">
                    <div id="editFormErrors" class="alert alert-danger" style="display: none;"></div>

                    <div class="form-group">
                        <label for="edit_status_name">Status Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_status_name" name="status_name" required>
                        <small class="form-text text-muted">Order cannot be changed for workflow integrity</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('page-scripts') ?>
<script>
// Connection Statuses JavaScript - will be loaded after jQuery
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        initializeConnectionStatuses();
    }
});

function initializeConnectionStatuses() {
    // Initialize DataTable
    $('.datatable').DataTable({
        ajax: {
            url: '<?= base_url('admin/connection-statuses/get-table-data') ?>',
            type: 'GET'
        },
        columns: [
            { data: 'index', orderable: false },
            { data: 'status_name' },
            { data: 'actions', orderable: false }
        ],
        order: [[0, 'asc']],
        destroy: true
    });

    // Create status
    window.createStatus = function() {
        $.ajax({
            url: '<?= base_url('admin/connection-statuses/create') ?>',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#create_status_name').val('');
                    $('#create_status_order').val(response.next_order);
                    $('#createStatusForm').removeAttr('data-id');
                    $('#createFormErrors').hide();
                    $('#createStatusModal').modal('show');
                } else {
                    toastr.error('Failed to prepare create form');
                }
            },
            error: function() {
                toastr.error('An error occurred while preparing the create form.');
            }
        });
    };

    // Edit status
    window.editStatus = function(id) {
        $.ajax({
            url: '<?= base_url('admin/connection-statuses/edit/') ?>' + id,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#edit_status_name').val(response.data.status_name);
                    $('#editStatusForm').attr('data-id', id);
                    $('#editFormErrors').hide();
                    $('#editStatusModal').modal('show');
                } else {
                    toastr.error(response.message || 'Failed to load status data');
                }
            },
            error: function() {
                toastr.error('An error occurred while loading the status data.');
            }
        });
    };

    // Handle edit form submission
    // Handle create form submission
    $('#createStatusForm').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Creating...');

        var formData = new FormData(this);
        var url = '<?= base_url('admin/connection-statuses/store') ?>';

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#createStatusModal').modal('hide');
                    $('.datatable').DataTable().ajax.reload();
                    toastr.success(response.message);
                    $('#createStatusForm')[0].reset();
                } else {
                    if (response.errors) {
                        var errors = '';
                        $.each(response.errors, function(key, value) {
                            errors += '<div>' + value + '</div>';
                        });
                        $('#createFormErrors').html(errors).show();
                    } else {
                        toastr.error(response.message || 'An error occurred');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                toastr.error('An error occurred while creating the status.');
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalText);
            }
        });

        return false;
    });

    $('#editStatusForm').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Updating...');

        var formData = new FormData(this);
        var statusId = $(this).attr('data-id');
        var url = '<?= base_url('admin/connection-statuses/update/') ?>' + statusId;

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#editStatusModal').modal('hide');
                    $('.datatable').DataTable().ajax.reload();
                    toastr.success(response.message);
                } else {
                    if (response.errors) {
                        var errors = '';
                        $.each(response.errors, function(key, value) {
                            errors += '<div>' + value + '</div>';
                        });
                        $('#editFormErrors').html(errors).show();
                    } else {
                        toastr.error(response.message || 'An error occurred');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                toastr.error('An error occurred while updating the status.');
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalText);
            }
        });

        return false;
    });
}
</script>
<?= $this->endSection() ?>