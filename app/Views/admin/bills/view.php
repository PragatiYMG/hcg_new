<?= $this->extend('admin/layout') ?>

<?= $this->section('styles') ?>
<style>
.tinymce-content {
    font-family: Arial, sans-serif;
    font-size: 14px;
    line-height: 1.6;
    word-wrap: break-word;
}
.tinymce-content h1, .tinymce-content h2, .tinymce-content h3, .tinymce-content h4, .tinymce-content h5, .tinymce-content h6 {
    margin-top: 1.5em;
    margin-bottom: 0.5em;
    font-weight: bold;
}
.tinymce-content p {
    margin: 0 0 1em 0;
}
.tinymce-content ul, .tinymce-content ol {
    margin: 1em 0;
    padding-left: 2em;
}
.tinymce-content blockquote {
    margin: 1em 2em;
    padding-left: 1em;
    border-left: 4px solid #ccc;
    font-style: italic;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">View Bill Version <?= esc($bill['version']) ?></h3>
                    <div class="card-tools">
                        <a href="<?= base_url('admin/bills') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Bills
                        </a>
                        <?php if ($canEdit && $bill['status'] !== 'active'): ?>
                        <a href="<?= base_url('admin/bills/edit/' . $bill['id']) ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <?php endif; ?>
                        <button class="btn btn-info btn-sm" onclick="duplicateBill(<?= $bill['id'] ?>)">
                            <i class="fas fa-copy"></i> Duplicate
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Status Badge -->
                    <div class="mb-3">
                        <span class="badge badge-<?= $bill['status'] == 'active' ? 'success' : ($bill['status'] == 'draft' ? 'warning' : 'secondary') ?> badge-lg">
                            <?= ucfirst($bill['status']) ?> Version
                        </span>
                        <?php if ($bill['effective_date']): ?>
                        <small class="text-muted ml-2">
                            Effective: <?= date('d M Y', strtotime($bill['effective_date'])) ?>
                        </small>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <!-- Company Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Company Information</h5>
                                </div>
                                <div class="card-body">
                                    <dl class="row">
                                        <dt class="col-sm-5">Company Name:</dt>
                                        <dd class="col-sm-7"><?= esc($bill['company_name']) ?></dd>

                                        <dt class="col-sm-5">Short Name:</dt>
                                        <dd class="col-sm-7"><?= esc($bill['company_name_short'] ?: '-') ?></dd>

                                        <dt class="col-sm-5">Tag Line:</dt>
                                        <dd class="col-sm-7"><?= esc($bill['tag_line'] ?: '-') ?></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Contact Information</h5>
                                </div>
                                <div class="card-body">
                                    <dl class="row">
                                        <dt class="col-sm-5">Customer Care Email:</dt>
                                        <dd class="col-sm-7"><?= esc($bill['customer_care_email']) ?></dd>

                                        <dt class="col-sm-5">Website:</dt>
                                        <dd class="col-sm-7">
                                            <?php if ($bill['website_link']): ?>
                                                <a href="<?= esc($bill['website_link']) ?>" target="_blank"><?= esc($bill['website_link']) ?> <i class="fas fa-external-link-alt"></i></a>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </dd>

                                        <dt class="col-sm-5">Emergency Contact:</dt>
                                        <dd class="col-sm-7"><?= esc($bill['emergency_contact']) ?></dd>

                                        <dt class="col-sm-5">Customer Care Phones:</dt>
                                        <dd class="col-sm-7"><?= nl2br(esc($bill['customer_care_phones'])) ?></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <!-- Addresses -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Addresses</h5>
                                </div>
                                <div class="card-body">
                                    <h6>Registered Office Address:</h6>
                                    <p class="mb-3"><?= nl2br(esc($bill['registered_office_address'] ?: '-')) ?></p>

                                    <h6>Corporate Office Address:</h6>
                                    <p class="mb-0"><?= nl2br(esc($bill['corporate_office_address'] ?: '-')) ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Registration Details -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Registration Details</h5>
                                </div>
                                <div class="card-body">
                                    <dl class="row">
                                        <dt class="col-sm-4">CIN:</dt>
                                        <dd class="col-sm-8"><?= esc($bill['cin_no'] ?: '-') ?></dd>

                                        <dt class="col-sm-4">GST:</dt>
                                        <dd class="col-sm-8"><?= esc($bill['gst_no'] ?: '-') ?></dd>

                                        <dt class="col-sm-4">TIN:</dt>
                                        <dd class="col-sm-8"><?= esc($bill['tin'] ?: '-') ?></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Images Section -->
                    <?php if ($logoImage || $signatureImage || $invoiceImage): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Images</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php if ($logoImage): ?>
                                        <div class="col-md-4">
                                            <h6>Logo:</h6>
                                            <img src="<?= base_url($logoImage['file_path']) ?>" alt="Logo" class="img-fluid" style="max-height: 100px;">
                                            <small class="text-muted d-block mt-1"><?= esc($logoImage['image_name']) ?></small>
                                        </div>
                                        <?php endif; ?>

                                        <?php if ($signatureImage): ?>
                                        <div class="col-md-4">
                                            <h6>Signature:</h6>
                                            <img src="<?= base_url($signatureImage['file_path']) ?>" alt="Signature" class="img-fluid" style="max-height: 100px;">
                                            <small class="text-muted d-block mt-1"><?= esc($signatureImage['image_name']) ?></small>
                                        </div>
                                        <?php endif; ?>

                                        <?php if ($invoiceImage): ?>
                                        <div class="col-md-4">
                                            <h6>Invoice Image:</h6>
                                            <img src="<?= base_url($invoiceImage['file_path']) ?>" alt="Invoice" class="img-fluid" style="max-height: 100px;">
                                            <small class="text-muted d-block mt-1"><?= esc($invoiceImage['image_name']) ?></small>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Content Section -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Content</h5>
                                </div>
                                <div class="card-body">
                                    <?php if ($bill['summary_description']): ?>
                                    <h6>Summary Description:</h6>
                                    <div class="mb-3 p-3 bg-light rounded tinymce-content">
                                        <?= $bill['summary_description'] ?>
                                    </div>
                                    <?php endif; ?>

                                    <?php if ($bill['invoice_text']): ?>
                                    <h6>Invoice Text:</h6>
                                    <div class="mb-3 p-3 bg-light rounded tinymce-content">
                                        <?= $bill['invoice_text'] ?>
                                    </div>
                                    <?php endif; ?>

                                    <?php if ($bill['footer_description']): ?>
                                    <h6>Footer Description:</h6>
                                    <div class="p-3 bg-light rounded tinymce-content">
                                        <?= $bill['footer_description'] ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metadata -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <small class="text-muted">
                                        Created: <?= date('d M Y H:i', strtotime($bill['created_at'])) ?>
                                        <?php if ($bill['created_by']): ?>
                                        by <?= esc($bill['created_by_name'] ?: 'Unknown') ?>
                                        <?php endif; ?>
                                        <?php if ($bill['updated_at'] && $bill['updated_at'] != $bill['created_at']): ?>
                                        | Last Updated: <?= date('d M Y H:i', strtotime($bill['updated_at'])) ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function duplicateBill(id) {
    if (confirm('Are you sure you want to duplicate this bill as a draft?')) {
        $.ajax({
            url: '<?= base_url('admin/bills/duplicate/') ?>' + id,
            type: 'POST',
            success: function(response) {
                if (response.success) {
                    window.location.href = '<?= base_url('admin/bills') ?>';
                } else {
                    alert(response.message || 'Failed to duplicate bill');
                }
            },
            error: function() {
                alert('An error occurred while duplicating the bill.');
            }
        });
    }
}
</script>
<?= $this->endSection() ?>