<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header row">
                    <h3 class="card-title col-6">Connection Fees Management</h3>
                    <div class="card-tools col-6 text-right">
                        <button class="btn btn-primary btn-sm" onclick="createFee()">
                            <i class="fas fa-plus"></i> Add New Fee
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped datatable" data-skip-auto-init="true">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Total Fee</th>
                                <th>Refundable Fee</th>
                                <th>Non-Refundable Fee</th>
                                <th>Effective Date</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="feeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Connection Fee</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="feeForm">
                <div class="modal-body">
                    <div id="editWarning" class="alert alert-warning" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Edit Mode:</strong> Only status can be changed. Fee amounts and effective date are locked for security reasons.
                    </div>
                    <div id="formErrors" class="alert alert-danger" style="display: none;"></div>

                    <div class="form-group">
                        <label for="refundable_fee">Refundable Fee (Rs.) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="refundable_fee" name="refundable_fee" required>
                    </div>

                    <div class="form-group">
                        <label for="non_refundable_fee">Non-Refundable Fee (Rs.) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="non_refundable_fee" name="non_refundable_fee" required>
                    </div>

                    <div class="form-group">
                        <label for="total_fee">Total Fee (Rs.) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="total_fee" name="total_fee" readonly required>
                        <small class="form-text text-muted">Auto-calculated from refundable + non-refundable fees</small>
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
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('page-scripts') ?>
<script>
// Connection Fees JavaScript - will be loaded after jQuery
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        initializeConnectionFees();
    }
});

function initializeConnectionFees() {
    // Destroy existing DataTable if it exists
    if ($.fn.DataTable.isDataTable('.datatable')) {
        $('.datatable').DataTable().destroy();
    }

    // Initialize DataTable with AJAX
    $('.datatable').DataTable({
        ajax: {
            url: '<?= base_url('admin/connection-fees/get-table-data') ?>',
            type: 'GET'
        },
        columns: [
            { data: 'index' },
            { data: 'total_fee' },
            { data: 'refundable_fee' },
            { data: 'non_refundable_fee' },
            { data: 'effective_date' },
            { data: 'status' },
            { data: 'created_by' },
            { data: 'actions', orderable: false }
        ],
        order: [[0, 'desc']],
        destroy: true // Allow reinitialization
    });

    // Auto-calculate total fee
    function calculateTotal() {
        var refundable = parseFloat($('#refundable_fee').val()) || 0;
        var nonRefundable = parseFloat($('#non_refundable_fee').val()) || 0;
        var total = refundable + nonRefundable;
        $('#total_fee').val(total.toFixed(2));
    }

    $('#refundable_fee, #non_refundable_fee').on('input', calculateTotal);

    // Make functions global
    window.createFee = function() {
        $('#modalTitle').text('Add Connection Fee');
        $('#feeForm')[0].reset();
        $('#formErrors').hide();
        $('#editWarning').hide();
        $('#feeForm').removeAttr('data-id');

        // Make all fields editable for new entries
        $('#total_fee, #refundable_fee, #non_refundable_fee, #effective_date').prop('readonly', false);
        $('#total_fee, #refundable_fee, #non_refundable_fee, #effective_date').removeClass('bg-light');
        $('#total_fee, #refundable_fee, #non_refundable_fee, #effective_date').removeAttr('title');

        calculateTotal(); // Reset total to 0.00
        $('#feeModal').modal('show');
    };

    window.editFee = function(id) {
        $('#modalTitle').text('Edit Connection Fee (Status Only)');
        $('#feeForm')[0].reset();
        $('#formErrors').hide();
        $('#editWarning').show(); // Show the warning for edit mode

        $.ajax({
            url: '<?= base_url('admin/connection-fees/edit/') ?>' + id,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    // Set values
                    $('#total_fee').val(response.data.total_fee);
                    $('#refundable_fee').val(response.data.refundable_fee);
                    $('#non_refundable_fee').val(response.data.non_refundable_fee);
                    $('#effective_date').val(response.data.effective_date);
                    $('#status').val(response.data.status);
                    $('#feeForm').attr('data-id', id);

                    // Make amount fields readonly for editing (only status can be changed)
                    $('#total_fee').prop('readonly', true);
                    $('#refundable_fee').prop('readonly', true);
                    $('#non_refundable_fee').prop('readonly', true);
                    $('#effective_date').prop('readonly', true);

                    // Add visual indication
                    $('#total_fee, #refundable_fee, #non_refundable_fee, #effective_date').addClass('bg-light');
                    $('#total_fee, #refundable_fee, #non_refundable_fee, #effective_date').attr('title', 'Read-only: Only status can be changed');

                    calculateTotal(); // Recalculate total after loading data
                    $('#feeModal').modal('show');
                } else {
                    toastr.error(response.message || 'Failed to load fee data');
                }
            },
            error: function() {
                toastr.error('An error occurred while loading the fee data.');
            }
        });
    };

    $('#feeForm').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        // Disable submit button to prevent double submission
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Saving...');

        var formData = new FormData(this);
        var feeId = $(this).attr('data-id');
        var url = feeId ? '<?= base_url('admin/connection-fees/update/') ?>' + feeId : '<?= base_url('admin/connection-fees/store') ?>';

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#feeModal').modal('hide');
                    $('.datatable').DataTable().ajax.reload();
                    toastr.success(response.message);
                    $('#feeForm')[0].reset();
                    $('#feeForm').removeAttr('data-id');
                } else {
                    if (response.errors) {
                        var errors = '';
                        $.each(response.errors, function(key, value) {
                            errors += '<div>' + value + '</div>';
                        });
                        $('#formErrors').html(errors).show();
                    } else {
                        toastr.error(response.message || 'An error occurred');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                toastr.error('An error occurred while saving the fee.');
            },
            complete: function() {
                // Re-enable submit button
                submitBtn.prop('disabled', false).text(originalText);
            }
        });

        return false;
    });
}
</script>
<?= $this->endSection() ?>