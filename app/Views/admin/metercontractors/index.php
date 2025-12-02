<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header row">
                    <h3 class="card-title col-6">Meter Contractors Management</h3>
                    <div class="card-tools col-6 text-right">
                        <button type="button" class="btn btn-primary btn-sm" onclick="createContractor()">
                            <i class="fas fa-plus"></i> Add New Contractor
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped datatable" data-skip-auto-init="true">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Contractor Name</th>
                                <th>Status</th>
                                <th>Created</th>
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

<!-- Create Contractor Modal -->
<div class="modal fade" id="createContractorModal" tabindex="-1" role="dialog" aria-labelledby="createContractorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createContractorModalLabel">Add New Meter Contractor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createContractorForm">
                <div class="modal-body">
                    <div id="createFormErrors" class="alert alert-danger" style="display: none;"></div>

                    <div class="form-group">
                        <label for="create_name">Contractor Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="create_name" name="name" required>
                        <small class="form-text text-muted">Enter the full name of the meter contractor</small>
                    </div>

                    <div class="form-group">
                        <label for="create_status">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="create_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <small class="form-text text-muted">Set the contractor as active or inactive</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Contractor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Contractor Modal -->
<div class="modal fade" id="editContractorModal" tabindex="-1" role="dialog" aria-labelledby="editContractorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editContractorModalLabel">Edit Meter Contractor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editContractorForm">
                <div class="modal-body">
                    <div id="editFormErrors" class="alert alert-danger" style="display: none;"></div>

                    <div class="form-group">
                        <label for="edit_name">Contractor Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                        <small class="form-text text-muted">Enter the full name of the meter contractor</small>
                    </div>

                    <div class="form-group">
                        <label for="edit_status">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <small class="form-text text-muted">Set the contractor as active or inactive</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Contractor</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('page-scripts') ?>
<script>
// Meter Contractors JavaScript - will be loaded after jQuery
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        initializeMeterContractors();
    }
});

function initializeMeterContractors() {
    // Initialize DataTable
    $('.datatable').DataTable({
        ajax: {
            url: '<?= base_url('admin/meter-contractors/get-table-data') ?>',
            type: 'GET'
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            {
                data: 'status',
                render: function(data, type, row) {
                    return '<span class="badge badge-' + (data == 'active' ? 'success' : 'danger') + '">' +
                           (data == 'active' ? 'Active' : 'Inactive') + '</span>';
                }
            },
            {
                data: 'created_info',
                orderable: false
            },
            { data: 'actions', orderable: false }
        ],
        order: [[0, 'desc']],
        destroy: true,
        processing: true,
        serverSide: true
    });

    // Create contractor
    window.createContractor = function() {
        $.ajax({
            url: '<?= base_url('admin/meter-contractors/create') ?>',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#create_name').val('');
                    $('#create_status').val('active');
                    $('#createContractorForm').removeAttr('data-id');
                    $('#createFormErrors').hide();
                    $('#createContractorModal').modal('show');
                } else {
                    toastr.error('Failed to prepare create form');
                }
            },
            error: function() {
                toastr.error('An error occurred while preparing the create form.');
            }
        });
    };

    // Edit contractor
    window.editContractor = function(id) {
        $.ajax({
            url: '<?= base_url('admin/meter-contractors/edit/') ?>' + id,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#edit_name').val(response.data.name);
                    $('#edit_status').val(response.data.status);
                    $('#editContractorForm').attr('data-id', id);
                    $('#editFormErrors').hide();
                    $('#editContractorModal').modal('show');
                } else {
                    toastr.error(response.message || 'Failed to load contractor data');
                }
            },
            error: function() {
                toastr.error('An error occurred while loading the contractor data.');
            }
        });
    };

    // Handle create form submission
    $('#createContractorForm').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Creating...');

        var formData = new FormData(this);
        var url = '<?= base_url('admin/meter-contractors/store') ?>';

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#createContractorModal').modal('hide');
                    $('.datatable').DataTable().ajax.reload();
                    toastr.success(response.message);
                    $('#createContractorForm')[0].reset();
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
                toastr.error('An error occurred while creating the contractor.');
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalText);
            }
        });

        return false;
    });

    // Handle edit form submission
    $('#editContractorForm').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Updating...');

        var formData = new FormData(this);
        var contractorId = $(this).attr('data-id');
        var url = '<?= base_url('admin/meter-contractors/update/') ?>' + contractorId;

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#editContractorModal').modal('hide');
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
                toastr.error('An error occurred while updating the contractor.');
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