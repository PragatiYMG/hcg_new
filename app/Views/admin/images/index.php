<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Image Management</h3>
                    <div>
                        <button class="btn btn-outline-secondary btn-sm" type="button" data-toggle="collapse" data-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                            <i class="fas fa-filter"></i> Filters
                        </button>
                        <a href="?export=csv<?= !empty($_GET) ? '&' . http_build_query(array_diff_key($_GET, ['export' => ''])) : '' ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addImageModal">
                            <i class="fas fa-plus"></i> Upload Image
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
                        <table class="table table-striped table-bordered" id="imagesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image Name</th>
                                    <th>Preview</th>
                                    <th>File Info</th>
                                    <th>Uploaded By</th>
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

<!-- Upload Image Modal -->
<div class="modal fade" id="addImageModal" tabindex="-1" role="dialog" aria-labelledby="addImageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addImageModalLabel">Upload New Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addImageForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div id="addImageErrors"></div>
                    <div class="form-group">
                        <label for="image_name">Image Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="image_name" name="image_name" required>
                        <small class="form-text text-muted">Enter a descriptive name for this image (used in invoices)</small>
                    </div>
                    <div class="form-group">
                        <label for="image_file">Image File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file" id="image_file" name="image_file" accept="image/*" required>
                        <small class="form-text text-muted">Supported formats: JPG, PNG, GIF, WebP. Max size: 2MB</small>
                        <div id="imagePreview" class="mt-2" style="display: none;">
                            <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload Image
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Image Modal -->
<div class="modal fade" id="editImageModal" tabindex="-1" role="dialog" aria-labelledby="editImageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editImageModalLabel">Edit Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editImageForm" enctype="multipart/form-data">
                <input type="hidden" id="edit_image_id" name="image_id">
                <div class="modal-body">
                    <div id="editImageErrors"></div>
                    <div class="form-group">
                        <label for="edit_image_name">Image Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_image_name" name="image_name" required>
                        <small class="form-text text-muted">Enter a descriptive name for this image</small>
                    </div>
                    <div class="form-group">
                        <label for="edit_image_file">Replace Image File (Optional)</label>
                        <input type="file" class="form-control-file" id="edit_image_file" name="image_file" accept="image/*">
                        <small class="form-text text-muted">Leave empty to keep current image. Supported formats: JPG, PNG, GIF, WebP. Max size: 2MB</small>
                        <div id="editImagePreview" class="mt-2">
                            <img id="editPreviewImg" src="" alt="Current Image" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Image
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
    const imagesTable = $('#imagesTable').DataTable({
        ajax: {
            url: '<?= base_url('admin/images/getTableData') ?>',
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
            { data: 'image_name', orderable: true },
            { data: 'image_preview', orderable: false },
            { data: 'file_info', orderable: false },
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
            emptyTable: "No images found."
        }
    });

    // Filter form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        imagesTable.ajax.reload();
    });

    // Clear filters
    $('#clearFilters').on('click', function() {
        $('#created_from').val('');
        $('#created_to').val('');
        $('#updated_from').val('');
        $('#updated_to').val('');
        imagesTable.ajax.reload();
    });

    // Image preview for upload
    $('#image_file').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImg').attr('src', e.target.result);
                $('#imagePreview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').hide();
        }
    });

    // Upload Image Form Submit
    $('#addImageForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            url: '<?= base_url('admin/images/store') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#addImageModal').modal('hide');
                    $('#addImageForm')[0].reset();
                    $('#imagePreview').hide();
                    showAlert('success', response.message);
                    // Reload table data without page refresh
                    imagesTable.ajax.reload(null, false);
                } else {
                    displayErrors('#addImageErrors', response.errors);
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while uploading the image.');
            }
        });
    });

    // Edit Image Form Submit
    $('#editImageForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const imageId = $('#edit_image_id').val();

        $.ajax({
            url: '<?= base_url('admin/images/update/') ?>' + imageId,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#editImageModal').modal('hide');
                    showAlert('success', response.message);
                    // Reload table data without page refresh
                    imagesTable.ajax.reload(null, false);
                } else {
                    displayErrors('#editImageErrors', response.errors);
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while updating the image.');
            }
        });
    });

    // Reset forms when modals are closed
    $('#addImageModal').on('hidden.bs.modal', function() {
        $('#addImageForm')[0].reset();
        $('#addImageErrors').html('');
        $('#imagePreview').hide();
    });

    $('#editImageModal').on('hidden.bs.modal', function() {
        $('#editImageForm')[0].reset();
        $('#editImageErrors').html('');
    });
});

function editImage(id) {
    // Get image data from server
    $.ajax({
        url: '<?= base_url('admin/images/getImage/') ?>' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                $('#edit_image_id').val(response.data.id);
                $('#edit_image_name').val(response.data.image_name);
                if (response.data.file_path) {
                    $('#editPreviewImg').attr('src', '<?= base_url() ?>' + response.data.file_path);
                    $('#editImagePreview').show();
                } else {
                    $('#editImagePreview').hide();
                }
                $('#editImageModal').modal('show');
            } else {
                showAlert('error', response.message || 'Failed to load image data.');
            }
        },
        error: function() {
            showAlert('error', 'Failed to load image data.');
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