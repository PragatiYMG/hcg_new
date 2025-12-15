<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Customer Details</h3>
                    <div>
                        <?php if (hasPermission('customers.edit')): ?>
                            <a href="<?= base_url('admin/customers/edit/' . $customer['id']) ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit Customer
                            </a>
                        <?php endif; ?>
                        <a href="<?= base_url('admin/customers') ?>" class="btn btn-secondary btn-sm ml-2">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Customer Photo -->
                    <div class="row mb-4">
                        <div class="col-md-12 text-center">
                            <?php if ($customer['customer_photo'] && file_exists(ROOTPATH . 'public/uploads/' . $customer['customer_photo'])): ?>
                                <img src="<?= base_url('uploads/' . $customer['customer_photo']) ?>" alt="Customer Photo" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                                    <i class="fas fa-user fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            <h4 class="mt-3 mb-1">
                                <?= esc($customer['first_name'] . ' ' . ($customer['middle_name'] ? $customer['middle_name'] . ' ' : '') . $customer['last_name']) ?>
                            </h4>
                            <span class="badge badge-<?= $customer['status'] === 'active' ? 'success' : ($customer['status'] === 'inactive' ? 'danger' : 'warning') ?> badge-lg">
                                <?= ucfirst($customer['status']) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="text-primary mb-3"><i class="fas fa-user"></i> Personal Information</h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">First Name:</label>
                                <p class="mb-0"><?= esc($customer['first_name']) ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Middle Name:</label>
                                <p class="mb-0"><?= esc($customer['middle_name'] ?: 'N/A') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Last Name:</label>
                                <p class="mb-0"><?= esc($customer['last_name']) ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Gender:</label>
                                <p class="mb-0">
                                    <span class="badge badge-<?= $customer['gender'] === 'male' ? 'primary' : ($customer['gender'] === 'female' ? 'success' : 'info') ?>">
                                        <?= ucfirst($customer['gender']) ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Date of Birth:</label>
                                <p class="mb-0">
                                    <?php
                                    if ($customer['date_of_birth']) {
                                        $dob = new DateTime($customer['date_of_birth']);
                                        echo $dob->format('d M Y') . ' (Age: ' . $dob->diff(new DateTime())->y . ' years)';
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Family Information -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="text-primary mb-3"><i class="fas fa-users"></i> Family Information</h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Father/Husband Name:</label>
                                <p class="mb-0"><?= esc($customer['father_husband_name']) ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Mother Name:</label>
                                <p class="mb-0"><?= esc($customer['mother_name'] ?: 'N/A') ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="text-primary mb-3"><i class="fas fa-phone"></i> Contact Information</h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Primary Mobile:</label>
                                <p class="mb-0">
                                    <i class="fas fa-phone mr-2"></i>
                                    <?= esc($customer['primary_mobile']) ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Alternate Mobile:</label>
                                <p class="mb-0">
                                    <?php if ($customer['alternate_mobile']): ?>
                                        <i class="fas fa-phone mr-2"></i>
                                        <?= esc($customer['alternate_mobile']) ?>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-bold">Email Address:</label>
                                <p class="mb-0">
                                    <i class="fas fa-envelope mr-2"></i>
                                    <a href="mailto:<?= esc($customer['email']) ?>"><?= esc($customer['email']) ?></a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Identity Information -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="text-primary mb-3"><i class="fas fa-id-card"></i> Identity Information</h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Aadhaar Number:</label>
                                <p class="mb-0">
                                    <?php
                                    $aadhaar = $customer['aadhaar_number'];
                                    echo substr($aadhaar, 0, 4) . ' ' . substr($aadhaar, 4, 4) . ' ' . substr($aadhaar, 8, 4);
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Aadhaar Attachment:</label>
                                <p class="mb-0">
                                    <?php if ($customer['aadhaar_attachment'] && file_exists(ROOTPATH . 'public/uploads/' . $customer['aadhaar_attachment'])): ?>
                                        <a href="<?= base_url('uploads/' . $customer['aadhaar_attachment']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View Aadhaar Document
                                        </a>
                                    <?php else: ?>
                                        No attachment uploaded
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <?php if ($customer['secondary_id_type']): ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Secondary ID Type:</label>
                                <p class="mb-0"><?= ucfirst(str_replace('_', ' ', $customer['secondary_id_type'])) ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Secondary ID Number:</label>
                                <p class="mb-0"><?= esc($customer['secondary_id_number'] ?: 'N/A') ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Secondary ID Attachment:</label>
                                <p class="mb-0">
                                    <?php if ($customer['secondary_id_attachment'] && file_exists(ROOTPATH . 'public/uploads/' . $customer['secondary_id_attachment'])): ?>
                                        <a href="<?= base_url('uploads/' . $customer['secondary_id_attachment']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View Secondary ID Document
                                        </a>
                                    <?php else: ?>
                                        No attachment uploaded
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- System Information -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="text-primary mb-3"><i class="fas fa-info-circle"></i> System Information</h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Customer ID:</label>
                                <p class="mb-0">
                                    <code><?= $customer['id'] ?></code>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Status:</label>
                                <p class="mb-0">
                                    <span class="badge badge-<?= $customer['status'] === 'active' ? 'success' : ($customer['status'] === 'inactive' ? 'danger' : 'warning') ?>">
                                        <?= ucfirst($customer['status']) ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Created Date:</label>
                                <p class="mb-0">
                                    <i class="fas fa-calendar mr-2"></i>
                                    <?= date('d M Y H:i:s', strtotime($customer['created_at'])) ?>
                                </p>
                                <?php if (isset($customer['created_by_name'])): ?>
                                    <small class="text-muted">by <?= esc($customer['created_by_name']) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Last Updated:</label>
                                <p class="mb-0">
                                    <i class="fas fa-calendar-check mr-2"></i>
                                    <?php if ($customer['updated_at'] && $customer['updated_at'] !== $customer['created_at']): ?>
                                        <?= date('d M Y H:i:s', strtotime($customer['updated_at'])) ?>
                                    <?php else: ?>
                                        Never updated
                                    <?php endif; ?>
                                </p>
                                <?php if (isset($customer['updated_by_name']) && $customer['updated_at'] && $customer['updated_at'] !== $customer['created_at']): ?>
                                    <small class="text-muted">by <?= esc($customer['updated_by_name']) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.badge-lg {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}

code {
    background-color: #f8f9fa;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}
</style>

<?= $this->endSection() ?>