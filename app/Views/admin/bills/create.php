<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Create New Bill Version</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('admin/bills') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Bills
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div id="createBillErrors"></div>

                    <form id="createBillForm" enctype="multipart/form-data">
                        <!-- Company Information -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="text-primary"><i class="fas fa-building"></i> Company Information</h5>
                                <hr>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="company_name">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="company_name" name="company_name" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="company_name_short">Company Name (Short)</label>
                                <input type="text" class="form-control" id="company_name_short" name="company_name_short">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tag_line">Tagline</label>
                            <input type="text" class="form-control" id="tag_line" name="tag_line">
                        </div>

                        <!-- Addresses -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5 class="text-primary"><i class="fas fa-map-marker-alt"></i> Addresses</h5>
                                <hr>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="registered_office_address">Registered Office Address</label>
                                <textarea class="form-control" id="registered_office_address" name="registered_office_address" rows="3"></textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="corporate_office_address">Corporate Office Address</label>
                                <textarea class="form-control" id="corporate_office_address" name="corporate_office_address" rows="3"></textarea>
                            </div>
                        </div>

                        <!-- Registration Numbers -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5 class="text-primary"><i class="fas fa-id-card"></i> Registration Numbers</h5>
                                <hr>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="cin_no">CIN No.</label>
                                <input type="text" class="form-control" id="cin_no" name="cin_no">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="gst_no">GST No.</label>
                                <input type="text" class="form-control" id="gst_no" name="gst_no">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="tin">TIN No.</label>
                                <input type="text" class="form-control" id="tin" name="tin">
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5 class="text-primary"><i class="fas fa-phone"></i> Contact Information</h5>
                                <hr>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="customer_care_email">Customer Care Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="customer_care_email" name="customer_care_email" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="website_link">Website Link <span class="text-danger">*</span></label>
                                <input type="url" class="form-control" id="website_link" name="website_link" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="emergency_contact">Emergency Contact <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="customer_care_phones">Customer Care Phones <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="customer_care_phones" name="customer_care_phones" rows="2" placeholder="Enter multiple phone numbers separated by commas" required></textarea>
                            </div>
                        </div>

                        <!-- Images -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5 class="text-primary"><i class="fas fa-images"></i> Images</h5>
                                <hr>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="logo_image_id">Logo Image</label>
                                <select class="form-control" id="logo_image_id" name="logo_image_id">
                                    <option value="">Select Logo Image</option>
                                    <?php foreach ($images as $image): ?>
                                        <option value="<?= $image['id'] ?>"><?= esc($image['image_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">Select a logo image from the Image Master</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="signature_image_id">Signature Image</label>
                                <select class="form-control" id="signature_image_id" name="signature_image_id">
                                    <option value="">Select Signature Image</option>
                                    <?php foreach ($images as $image): ?>
                                        <option value="<?= $image['id'] ?>"><?= esc($image['image_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">Select a signature image from the Image Master</small>
                            </div>
                        </div>

                        <!-- Versioning -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5 class="text-primary"><i class="fas fa-code-branch"></i> Versioning</h5>
                                <hr>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="effective_date">Effective Date</label>
                                <input type="date" class="form-control" id="effective_date" name="effective_date">
                                <small class="form-text text-muted">Date when this bill version becomes effective</small>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="alert alert-info mt-4">
                                    <strong>Note:</strong> New bills are created as drafts. You can activate them later.
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5 class="text-primary"><i class="fas fa-file-alt"></i> Content</h5>
                                <hr>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="invoice_text">Invoice Text</label>
                            <textarea class="form-control" id="invoice_text" name="invoice_text" rows="4"></textarea>
                            <small class="form-text text-muted">Additional text to show on invoices</small>
                        </div>

                        <div class="form-group">
                            <label for="invoice_image_id">Invoice Image</label>
                            <select class="form-control" id="invoice_image_id" name="invoice_image_id">
                                <option value="">Select Invoice Image</option>
                                <?php foreach ($images as $image): ?>
                                    <option value="<?= $image['id'] ?>"><?= esc($image['image_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Select an additional image to show on invoices</small>
                        </div>

                        <div class="form-group">
                            <label for="summary_description">Summary Description</label>
                            <textarea class="form-control" id="summary_description" name="summary_description" rows="4"></textarea>
                            <small class="form-text text-muted">Summary description for the bill</small>
                        </div>

                        <div class="form-group">
                            <label for="footer_description">Footer Description</label>
                            <textarea class="form-control" id="footer_description" name="footer_description" rows="4"></textarea>
                            <small class="form-text text-muted">Footer description for the bill</small>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= base_url('admin/bills') ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Bill (Draft)
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.tiny.cloud/1/sn421l8x2iqlxwohjwsp5c0m4tz5rz8o1n6ow1bqlpjrolzx/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
$(document).ready(function() {
    // Initialize TinyMCE for rich text fields
    tinymce.init({
        selector: '#invoice_text, #summary_description, #footer_description',
        height: 200,
        menubar: false,
        plugins: 'lists link image code',
        toolbar: 'bold italic underline | bullist numlist | link image | code',
        content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }'
    });

    // Create Bill Form Submit
    $('#createBillForm').on('submit', function(e) {
        e.preventDefault();

        // TinyMCE automatically updates textareas

        const formData = new FormData(this);

        $.ajax({
            url: '<?= base_url('admin/bills/store') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    window.location.href = '<?= base_url('admin/bills') ?>';
                } else {
                    displayErrors('#createBillErrors', response.errors);
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while creating the bill.');
            }
        });
    });
});

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