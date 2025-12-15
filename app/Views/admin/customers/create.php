<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Add New Customer</h3>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-ban"></i> Error!</h5>
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <form id="customerForm" action="<?= base_url('admin/customers/store') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <!-- Personal Information -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="text-primary mb-3"><i class="fas fa-user"></i> Personal Information</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                           value="<?= old('first_name') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="middle_name">Middle Name</label>
                                    <input type="text" class="form-control" id="middle_name" name="middle_name"
                                           value="<?= old('middle_name') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name"
                                           value="<?= old('last_name') ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="gender">Gender <span class="text-danger">*</span></label>
                                    <select class="form-control" id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" <?= old('gender') === 'male' ? 'selected' : '' ?>>Male</option>
                                        <option value="female" <?= old('gender') === 'female' ? 'selected' : '' ?>>Female</option>
                                        <option value="other" <?= old('gender') === 'other' ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date_of_birth">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                           value="<?= old('date_of_birth') ?>" required
                                           min="1920-01-01" max="<?= date('Y-m-d', strtotime('-18 years')) ?>">
                                    <small class="form-text text-muted">Must be 18+ years old, birth year from 1920 onwards</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="customer_photo">Customer Photo</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="customer_photo" name="customer_photo"
                                               accept="image/*">
                                        <label class="custom-file-label" for="customer_photo">Choose photo file</label>
                                    </div>
                                    <small class="form-text text-muted">Max 2MB, JPG/PNG/WebP formats</small>
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
                                    <label for="father_husband_name">Father/Husband Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="father_husband_name" name="father_husband_name"
                                           value="<?= old('father_husband_name') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mother_name">Mother Name</label>
                                    <input type="text" class="form-control" id="mother_name" name="mother_name"
                                           value="<?= old('mother_name') ?>">
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
                                    <label for="primary_mobile">Primary Mobile Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="primary_mobile" name="primary_mobile"
                                           value="<?= old('primary_mobile') ?>" required
                                           pattern="^[6-9]\d{9}$" maxlength="10"
                                           placeholder="10-digit mobile number">
                                    <small class="form-text text-muted">Must start with 6,7,8, or 9</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="alternate_mobile">Alternate Mobile Number</label>
                                    <input type="text" class="form-control" id="alternate_mobile" name="alternate_mobile"
                                           value="<?= old('alternate_mobile') ?>"
                                           pattern="^[6-9]\d{9}$" maxlength="10"
                                           placeholder="10-digit mobile number">
                                    <small class="form-text text-muted">Must start with 6,7,8, or 9</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="<?= old('email') ?>" required>
                                    <small class="form-text text-muted">Will be used for login and communication</small>
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
                                    <label for="aadhaar_number">Aadhaar Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="aadhaar_number" name="aadhaar_number"
                                           value="<?= old('aadhaar_number') ?>" required
                                           pattern="^\d{12}$" maxlength="12"
                                           placeholder="12-digit Aadhaar number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="aadhaar_attachment">Aadhaar Attachment</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="aadhaar_attachment" name="aadhaar_attachment"
                                               accept="image/*,.pdf">
                                        <label class="custom-file-label" for="aadhaar_attachment">Choose Aadhaar file</label>
                                    </div>
                                    <small class="form-text text-muted">Max 2MB, JPG/PNG/PDF formats</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="secondary_id_type">Secondary ID Type</label>
                                    <select class="form-control" id="secondary_id_type" name="secondary_id_type">
                                        <option value="">Select ID Type</option>
                                        <option value="voter_id" <?= old('secondary_id_type') === 'voter_id' ? 'selected' : '' ?>>Voter ID</option>
                                        <option value="passport" <?= old('secondary_id_type') === 'passport' ? 'selected' : '' ?>>Passport</option>
                                        <option value="driving_license" <?= old('secondary_id_type') === 'driving_license' ? 'selected' : '' ?>>Driving License</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="secondary_id_number">Secondary ID Number</label>
                                    <input type="text" class="form-control" id="secondary_id_number" name="secondary_id_number"
                                           value="<?= old('secondary_id_number') ?>" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="secondary_id_attachment">Secondary ID Attachment</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="secondary_id_attachment" name="secondary_id_attachment"
                                               accept="image/*,.pdf">
                                        <label class="custom-file-label" for="secondary_id_attachment">Choose ID file</label>
                                    </div>
                                    <small class="form-text text-muted">Max 2MB, JPG/PNG/PDF formats</small>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5 class="text-primary mb-3"><i class="fas fa-cog"></i> Settings</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="pending" <?= old('status', 'pending') === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="active" <?= old('status') === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Customer
                                </button>
                                <a href="<?= base_url('admin/customers') ?>" class="btn btn-secondary ml-2">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Clear any existing errors on page load
document.addEventListener('DOMContentLoaded', function() {
    clearFormErrors();
});

// Format Aadhaar number input
document.getElementById('aadhaar_number').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 12) {
        value = value.slice(0, 12);
    }
    e.target.value = value;
});

// Format mobile number inputs
document.getElementById('primary_mobile').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 10) {
        value = value.slice(0, 10);
    }
    e.target.value = value;
});

document.getElementById('alternate_mobile').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 10) {
        value = value.slice(0, 10);
    }
    e.target.value = value;
});

// Update file input labels
document.querySelectorAll('.custom-file-input').forEach(function(input) {
    input.addEventListener('change', function(e) {
        var fileName = e.target.files[0] ? e.target.files[0].name : 'Choose file';
        var label = e.target.nextElementSibling;
        label.textContent = fileName;
    });
});

// Handle form submission with AJAX
document.getElementById('customerForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Always prevent default form submission

    var form = this;
    var formData = new FormData(form);
    var submitBtn = document.querySelector('button[type="submit"]');
    var originalText = submitBtn.innerHTML;

    // Clear previous errors
    clearFormErrors();

    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';

    // Send AJAX request
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
        }
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            // Success - show notification and redirect after delay
            toastr.success(data.message || 'Customer created successfully!');

            // Disable form to prevent any further submissions
            form.querySelectorAll('input, select, textarea, button').forEach(function(el) {
                el.disabled = true;
            });

            // Redirect after 2 seconds
            setTimeout(function() {
                window.location.href = data.redirect;
            }, 2000);
        } else {
            // Show errors
            if (data.errors) {
                displayFormErrors(data.errors);
            }
            // Show general error message
            showAlert('danger', data.message || 'An error occurred');
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while creating the customer');
    })
    .finally(function() {
        // Re-enable button (only if not successful)
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });

    return false; // Extra prevention of form submission
});

function clearFormErrors() {
    // Remove error classes and messages
    document.querySelectorAll('.is-invalid').forEach(function(el) {
        el.classList.remove('is-invalid');
    });
    document.querySelectorAll('.invalid-feedback').forEach(function(el) {
        el.remove();
    });
    // Hide alert
    var alertDiv = document.getElementById('formAlert');
    if (alertDiv) {
        alertDiv.style.display = 'none';
    }
}

function displayFormErrors(errors) {
    for (var field in errors) {
        var input = document.getElementById(field) || document.querySelector('[name="' + field + '"]');
        if (input) {
            input.classList.add('is-invalid');

            // Create error message element
            var errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = errors[field];

            // Insert after input
            input.parentNode.insertBefore(errorDiv, input.nextSibling);
        }
    }
}

function showAlert(type, message) {
    var alertDiv = document.getElementById('formAlert');
    if (!alertDiv) {
        // Create alert div if it doesn't exist
        alertDiv = document.createElement('div');
        alertDiv.id = 'formAlert';
        alertDiv.className = 'alert alert-dismissible';
        var closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'close';
        closeBtn.setAttribute('data-dismiss', 'alert');
        closeBtn.innerHTML = '&times;';
        alertDiv.appendChild(closeBtn);

        // Insert at the top of the form
        var form = document.getElementById('customerForm');
        form.insertBefore(alertDiv, form.firstChild);
    }

    alertDiv.className = 'alert alert-' + type + ' alert-dismissible';
    alertDiv.innerHTML = '<button type="button" class="close" data-dismiss="alert">&times;</button>' + message;
    alertDiv.style.display = 'block';
}
</script>
<?= $this->endSection() ?>