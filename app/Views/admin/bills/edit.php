<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Edit Bill Version <?= esc($bill['version']) ?></h3>
                    <div class="card-tools">
                        <a href="<?= base_url('admin/bills') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Bills
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Status Badge -->
                    <div class="mb-3">
                        <?php
                        $statusClass = '';
                        $statusText = '';
                        switch ($bill['status']) {
                            case 'active':
                                $statusClass = 'badge-success';
                                $statusText = 'Active';
                                break;
                            case 'draft':
                                $statusClass = 'badge-warning';
                                $statusText = 'Draft';
                                break;
                            case 'inactive':
                                $statusClass = 'badge-secondary';
                                $statusText = 'Inactive';
                                break;
                        }
                        ?>
                        <span class="badge <?= $statusClass ?> badge-lg">
                            <i class="fas fa-circle"></i> <?= $statusText ?> Version
                        </span>
                        <?php if ($bill['effective_date']): ?>
                            <small class="text-muted ml-2">
                                Effective: <?= date('d M Y', strtotime($bill['effective_date'])) ?>
                            </small>
                        <?php endif; ?>
                    </div>

                    <div id="editBillErrors"></div>

                    <form id="editBillForm" enctype="multipart/form-data">
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
                                <input type="text" class="form-control" id="company_name" name="company_name"
                                       value="<?= esc(old('company_name', $bill['company_name'])) ?>" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="company_name_short">Company Name (Short)</label>
                                <input type="text" class="form-control" id="company_name_short" name="company_name_short"
                                       value="<?= esc(old('company_name_short', $bill['company_name_short'])) ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tag_line">Tagline</label>
                            <input type="text" class="form-control" id="tag_line" name="tag_line"
                                   value="<?= esc(old('tag_line', $bill['tag_line'])) ?>">
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
                                <textarea class="form-control" id="registered_office_address" name="registered_office_address" rows="3"><?= esc(old('registered_office_address', $bill['registered_office_address'])) ?></textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="corporate_office_address">Corporate Office Address</label>
                                <textarea class="form-control" id="corporate_office_address" name="corporate_office_address" rows="3"><?= esc(old('corporate_office_address', $bill['corporate_office_address'])) ?></textarea>
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
                                <input type="text" class="form-control" id="cin_no" name="cin_no"
                                       value="<?= esc(old('cin_no', $bill['cin_no'])) ?>">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="gst_no">GST No.</label>
                                <input type="text" class="form-control" id="gst_no" name="gst_no"
                                       value="<?= esc(old('gst_no', $bill['gst_no'])) ?>">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="tin">TIN No.</label>
                                <input type="text" class="form-control" id="tin" name="tin"
                                       value="<?= esc(old('tin', $bill['tin'])) ?>">
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
                                <input type="email" class="form-control" id="customer_care_email" name="customer_care_email"
                                       value="<?= esc(old('customer_care_email', $bill['customer_care_email'])) ?>" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="website_link">Website Link <span class="text-danger">*</span></label>
                                <input type="url" class="form-control" id="website_link" name="website_link"
                                       value="<?= esc(old('website_link', $bill['website_link'])) ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="emergency_contact">Emergency Contact <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="emergency_contact" name="emergency_contact"
                                       value="<?= esc(old('emergency_contact', $bill['emergency_contact'])) ?>" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="customer_care_phones">Customer Care Phones <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="customer_care_phones" name="customer_care_phones" rows="2"
                                          placeholder="Enter multiple phone numbers separated by commas" required><?= esc(old('customer_care_phones', $bill['customer_care_phones'])) ?></textarea>
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
                                        <option value="<?= $image['id'] ?>" <?= ($bill['logo_image_id'] == $image['id']) ? 'selected' : '' ?>>
                                            <?= esc($image['image_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">Select a logo image from the Image Master</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="signature_image_id">Signature Image</label>
                                <select class="form-control" id="signature_image_id" name="signature_image_id">
                                    <option value="">Select Signature Image</option>
                                    <?php foreach ($images as $image): ?>
                                        <option value="<?= $image['id'] ?>" <?= ($bill['signature_image_id'] == $image['id']) ? 'selected' : '' ?>>
                                            <?= esc($image['image_name']) ?>
                                        </option>
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
                                <input type="date" class="form-control" id="effective_date" name="effective_date"
                                       value="<?= old('effective_date', $bill['effective_date'] ? date('Y-m-d', strtotime($bill['effective_date'])) : '') ?>">
                                <small class="form-text text-muted">Date when this bill version becomes effective</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Current Version</label>
                                <input type="text" class="form-control" value="<?= esc($bill['version']) ?>" readonly>
                                <small class="form-text text-muted">Version cannot be changed after creation</small>
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
                            <textarea class="form-control" id="invoice_text" name="invoice_text" rows="4"><?= esc(old('invoice_text', $bill['invoice_text'])) ?></textarea>
                            <small class="form-text text-muted">Additional text to show on invoices</small>
                        </div>

                        <div class="form-group">
                            <label for="invoice_image_id">Invoice Image</label>
                            <select class="form-control" id="invoice_image_id" name="invoice_image_id">
                                <option value="">Select Invoice Image</option>
                                <?php foreach ($images as $image): ?>
                                    <option value="<?= $image['id'] ?>" <?= ($bill['invoice_image_id'] == $image['id']) ? 'selected' : '' ?>>
                                        <?= esc($image['image_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Select an additional image to show on invoices</small>
                        </div>

                        <div class="form-group">
                            <label for="summary_description">Summary Description</label>
                            <textarea class="form-control" id="summary_description" name="summary_description" rows="4"><?= esc(old('summary_description', $bill['summary_description'])) ?></textarea>
                            <small class="form-text text-muted">Summary description for the bill</small>
                        </div>

                        <div class="form-group">
                            <label for="footer_description">Footer Description</label>
                            <textarea class="form-control" id="footer_description" name="footer_description" rows="4"><?= esc(old('footer_description', $bill['footer_description'])) ?></textarea>
                            <small class="form-text text-muted">Footer description for the bill</small>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= base_url('admin/bills') ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Bill
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

    // Edit Bill Form Submit
    $('#editBillForm').on('submit', function(e) {
        e.preventDefault();

        // TinyMCE automatically updates textareas

        const formData = new FormData(this);
        const billId = <?= $bill['id'] ?>;

        $.ajax({
            url: '<?= base_url('admin/bills/update/') ?>' + billId,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    window.location.href = '<?= base_url('admin/bills') ?>';
                } else {
                    displayErrors('#editBillErrors', response.errors);
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while updating the bill.');
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