<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
   <div class="row">
      <div class="col-12">
         <div class="card">
            <div class="card-header">
               <h3 class="card-title mb-0">Settings</h3>
            </div>
            <div class="card-body">
               <?php if (session()->getFlashdata('success')): ?>
                  <div class="alert alert-success alert-dismissible">
                     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                     <h5><i class="icon fas fa-check"></i> Success!</h5>
                     <?= session()->getFlashdata('success') ?>
                  </div>
               <?php endif; ?>

               <?php if (session()->getFlashdata('error')): ?>
                  <div class="alert alert-danger alert-dismissible">
                     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                     <h5><i class="icon fas fa-ban"></i> Error!</h5>
                     <?= session()->getFlashdata('error') ?>
                  </div>
               <?php endif; ?>
   
               <?php if (session()->getFlashdata('errors')): ?>
                  <div class="alert alert-danger alert-dismissible">
                     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                     <h5><i class="icon fas fa-ban"></i> Validation Errors!</h5>
                     <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $field => $error): ?>
                           <li><strong><?= ucfirst(str_replace('_', ' ', $field)) ?>:</strong> <?= $error ?></li>
                        <?php endforeach; ?>
                     </ul>
                  </div>
               <?php endif; ?>

               <!-- Nav tabs -->
               <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                  <li class="nav-item">
                     <a class="nav-link active" id="basic-tab" data-toggle="tab" href="#basic" role="tab" aria-controls="basic" aria-selected="true">
                        <i class="fas fa-cogs"></i> Basic Settings
                     </a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false">
                        <i class="fas fa-envelope"></i> Email Settings
                     </a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="service-location-tab" data-toggle="tab" href="#service-location" role="tab" aria-controls="service-location" aria-selected="false">
                        <i class="fas fa-map-marker-alt"></i> Service Location
                     </a>
                  </li>
               </ul>

               <!-- Tab panes -->
               <div class="tab-content mt-4" id="settingsTabContent">
                  <!-- Basic Settings Tab -->
                  <div class="tab-pane fade show active" id="basic" role="tabpanel" aria-labelledby="basic-tab">
                     <form action="<?= base_url('admin/settings/update') ?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="tab" value="basic">

                        <div class="form-group row mb-4">
                           <label class="col-md-3 col-form-label">Full Name</label>
                           <div class="col-md-9">
                              <input type="text" class="form-control" name="site_full_name" value="<?= esc($settings['site_full_name'] ?? '') ?>" placeholder="Enter full site name">
                              <small class="form-text text-muted">The complete name of your website/application</small>
                           </div>
                        </div>

                        <div class="form-group row mb-4">
                           <label class="col-md-3 col-form-label">Short Name</label>
                           <div class="col-md-9">
                              <input type="text" class="form-control" name="site_short_name" value="<?= esc($settings['site_short_name'] ?? '') ?>" placeholder="Enter short name">
                              <small class="form-text text-muted">A shorter version of your site name for navigation</small>
                           </div>
                        </div>

                        <div class="form-group row mb-4">
                           <label class="col-md-3 col-form-label">Site Logo</label>
                           <div class="col-md-9">
                              <div class="custom-file">
                                 <input type="file" class="custom-file-input" id="logo" name="logo" accept="image/*">
                                 <label class="custom-file-label" for="logo">Choose logo file (max 2MB)</label>
                              </div>
                              <small class="form-text text-muted">Recommended size: 180x50px. Formats: JPG, PNG, WebP</small>
                              <?php if (!empty($site_logo) && file_exists(ROOTPATH . 'public/uploads/' . $site_logo)): ?>
                                 <div class="mt-2">
                                    <p class="mb-1">Current Logo:</p>
                                    <img src="<?= base_url('uploads/' . $site_logo) ?>" alt="Current Logo" class="img-thumbnail" style="max-width: 80px; max-height: 80px;">
                                 </div>
                              <?php endif; ?>
                           </div>
                        </div>

                        <div class="form-group row mb-4">
                           <label class="col-md-3 col-form-label">Favicon</label>
                           <div class="col-md-9">
                              <div class="custom-file">
                                 <input type="file" class="custom-file-input" id="favicon" name="favicon" accept=".ico,image/x-icon,image/vnd.microsoft.icon,image/png">
                                 <label class="custom-file-label" for="favicon">Choose favicon file (max 1MB)</label>
                              </div>
                              <small class="form-text text-muted">Recommended size: 32x32px or 64x64px. Formats: ICO, PNG, JPG</small>
                              <?php if (!empty($site_favicon) && file_exists(ROOTPATH . 'public/uploads/' . $site_favicon)): ?>
                                 <div class="mt-2">
                                    <p class="mb-1">Current Favicon:</p>
                                    <img src="<?= base_url('uploads/' . $site_favicon) ?>" alt="Current Favicon" class="img-thumbnail" style="max-width: 32px; max-height: 32px;">
                                 </div>
                              <?php endif; ?>
                           </div>
                        </div>

                        <div class="form-group row mb-4">
                           <label class="col-md-3 col-form-label">Copyright</label>
                           <div class="col-md-9">
                              <input type="text" class="form-control" name="site_copyright" value="<?= esc($settings['site_copyright'] ?? '') ?>" placeholder="Enter copyright text">
                              <small class="form-text text-muted">Copyright notice displayed in footer</small>
                           </div>
                        </div>

                        <div class="form-group row">
                           <div class="col-md-9 offset-md-3">
                              <button type="submit" class="btn btn-primary">
                                 <i class="fas fa-save"></i> Update Basic Settings
                              </button>
                              <a href="<?= base_url('admin/settings') ?>" class="btn btn-secondary ml-2">
                                 Cancel
                              </a>
                           </div>
                        </div>
                     </form>
                  </div>

                  <!-- Email Settings Tab -->
                  <div class="tab-pane fade" id="email" role="tabpanel" aria-labelledby="email-tab">
                     <form action="<?= base_url('admin/settings/update') ?>" method="post">
                        <input type="hidden" name="tab" value="email">

                        <div class="form-group row mb-4">
                           <label class="col-md-3 col-form-label">SMTP Host</label>
                           <div class="col-md-9">
                              <input type="text" class="form-control" name="smtp_host" value="<?= esc($settings['smtp_host'] ?? '') ?>" placeholder="smtp.gmail.com">
                              <small class="form-text text-muted">SMTP server hostname</small>
                           </div>
                        </div>

                        <div class="form-group row mb-4">
                           <label class="col-md-3 col-form-label">SMTP Port</label>
                           <div class="col-md-9">
                              <input type="number" class="form-control" name="smtp_port" value="<?= esc($settings['smtp_port'] ?? '587') ?>" placeholder="587">
                              <small class="form-text text-muted">Common ports: 587 (TLS), 465 (SSL), 25 (non-encrypted)</small>
                           </div>
                        </div>

                        <div class="form-group row mb-4">
                           <label class="col-md-3 col-form-label">SMTP Username</label>
                           <div class="col-md-9">
                              <input type="text" class="form-control" name="smtp_username" value="<?= esc($settings['smtp_username'] ?? '') ?>" placeholder="apikey or your-email@gmail.com">
                              <small class="form-text text-muted">Your username for SMTP authentication (e.g., apikey for SendGrid)</small>
                           </div>
                        </div>

                        <div class="form-group row mb-4">
                           <label class="col-md-3 col-form-label">SMTP Password</label>
                           <div class="col-md-9">
                              <input type="password" class="form-control" name="smtp_password" value="<?= esc($settings['smtp_password'] ?? '') ?>" placeholder="Your SMTP password">
                              <small class="form-text text-muted">For Gmail, use App Password if 2FA is enabled</small>
                           </div>
                        </div>

                        <div class="form-group row mb-4">
                           <label class="col-md-3 col-form-label">SMTP Encryption</label>
                           <div class="col-md-9">
                              <select class="form-control" name="smtp_encryption">
                                 <option value="tls" <?= ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
                                 <option value="ssl" <?= ($settings['smtp_encryption'] ?? 'tls') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                 <option value="none" <?= ($settings['smtp_encryption'] ?? 'tls') === 'none' ? 'selected' : '' ?>>None</option>
                              </select>
                              <small class="form-text text-muted">Encryption method for SMTP connection</small>
                           </div>
                        </div>

                        <div class="form-group row mb-4">
                           <label class="col-md-3 col-form-label">From Email</label>
                           <div class="col-md-9">
                              <input type="email" class="form-control" name="from_email" value="<?= esc($settings['from_email'] ?? '') ?>" placeholder="noreply@yourdomain.com">
                              <small class="form-text text-muted">Default sender email address</small>
                           </div>
                        </div>

                        <div class="form-group row mb-4">
                           <label class="col-md-3 col-form-label">From Name</label>
                           <div class="col-md-9">
                              <input type="text" class="form-control" name="from_name" value="<?= esc($settings['from_name'] ?? '') ?>" placeholder="Your Site Name">
                              <small class="form-text text-muted">Default sender name</small>
                           </div>
                        </div>

                        <div class="form-group row">
                           <div class="col-md-9 offset-md-3">
                              <button type="submit" class="btn btn-primary">
                                 <i class="fas fa-save"></i> Update Email Settings
                              </button>
                              <button type="button" class="btn btn-info ml-2" onclick="testEmailSettings()">
                                 <i class="fas fa-envelope"></i> Test Email Settings
                              </button>
                              <a href="<?= base_url('admin/settings') ?>" class="btn btn-secondary ml-2">
                                 Cancel
                              </a>
                           </div>
                        </div>
                     </form>
                  </div>

                  <!-- Service Location Settings Tab -->
                  <div class="tab-pane fade" id="service-location" role="tabpanel" aria-labelledby="service-location-tab">
                     <div class="row">
                        <div class="col-md-8">
                           <form id="serviceLocationForm">
                              <input type="hidden" name="tab" value="service_location">

                              <div class="form-group row mb-4">
                                 <label class="col-md-4 col-form-label">State Name <span class="text-danger">*</span></label>
                                 <div class="col-md-8">
                                    <input type="text" class="form-control" id="state_name" name="state_name" value="<?= esc($settings['service_location']['state_name'] ?? '') ?>" placeholder="Enter state name">
                                    <small class="form-text text-muted">The state where service is available</small>
                                 </div>
                              </div>

                              <div class="form-group row mb-4">
                                 <label class="col-md-4 col-form-label">City Name <span class="text-danger">*</span></label>
                                 <div class="col-md-8">
                                    <input type="text" class="form-control" id="city_name" name="city_name" value="<?= esc($settings['service_location']['city_name'] ?? '') ?>" placeholder="Enter city name">
                                    <small class="form-text text-muted">The city where service is available</small>
                                 </div>
                              </div>

                              <div class="form-group row mb-4">
                                 <label class="col-md-4 col-form-label">Serviceable Pincodes <span class="text-danger">*</span></label>
                                 <div class="col-md-8">
                                    <textarea class="form-control" id="serviceable_pincodes" name="serviceable_pincodes" rows="4" placeholder="Enter pincodes separated by commas (e.g., 110001, 110002, 110003)"><?= esc($settings['service_location']['serviceable_pincodes'] ?? '') ?></textarea>
                                    <small class="form-text text-muted">Enter pincodes where PNG connection service is available, separated by commas</small>
                                 </div>
                              </div>

                              <div class="form-group row">
                                 <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary" id="saveServiceLocationBtn">
                                       <i class="fas fa-save"></i> Save Service Location
                                    </button>
                                    <button type="button" class="btn btn-secondary ml-2" onclick="resetServiceLocationForm()">
                                       <i class="fas fa-undo"></i> Reset
                                    </button>
                                 </div>
                              </div>
                           </form>
                        </div>

                        <div class="col-md-4">
                           <div class="card">
                              <div class="card-header">
                                 <h5 class="card-title mb-0"><i class="fas fa-info-circle"></i> Information</h5>
                              </div>
                              <div class="card-body">
                                 <p class="mb-2"><strong>State Name:</strong> The primary state for PNG connections.</p>
                                 <p class="mb-2"><strong>City Name:</strong> The primary city for PNG connections.</p>
                                 <p class="mb-2"><strong>Serviceable Pincodes:</strong> List of pincodes where PNG connection services are available.</p>
                                 <p class="mb-0"><strong>Note:</strong> These settings will be used when creating PNG connections for customers.</p>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>


<!-- Test Email Modal -->
<div class="modal fade" id="testEmailModal" tabindex="-1" role="dialog" aria-labelledby="testEmailModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="testEmailModalLabel">Test Email Settings</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <form id="testEmailForm" method="post">
            <div class="modal-body">
               <div id="testEmailAlert" class="alert" style="display: none;" role="alert"></div>

               <div class="form-group">
                  <label for="test_recipient_email">Recipient Email <span class="text-danger">*</span></label>
                  <input type="email" class="form-control" id="test_recipient_email" name="recipient_email" required>
                  <small class="form-text text-muted">Email address where the test message will be sent</small>
               </div>

               <div class="form-group">
                  <label for="test_email_message">Test Message <span class="text-danger">*</span></label>
                  <textarea class="form-control" id="test_email_message" name="message" rows="4" required placeholder="Enter your test message here..."></textarea>
                  <small class="form-text text-muted">Message to send in the test email</small>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary" id="sendTestEmailBtn">
                  <i class="fas fa-paper-plane"></i> Send Test Email
               </button>
            </div>
         </form>
      </div>
   </div>
</div>

<script>
// Vanilla JavaScript implementation for email test functionality
document.addEventListener('DOMContentLoaded', function() {
   // Handle tab persistence and URL hash
   var hash = window.location.hash;
   if (hash) {
      var tabId = hash.substring(1);
      var tabElement = document.querySelector('.nav-tabs a[href="#' + tabId + '"]');
      if (tabElement) {
         tabElement.click();
      }
   }

   // Update URL hash when tab is changed
   var tabLinks = document.querySelectorAll('.nav-tabs a');
   tabLinks.forEach(function(link) {
      link.addEventListener('shown.bs.tab', function(e) {
         window.location.hash = e.target.hash;
      });
   });
});

function testEmailSettings() {
   var modal = document.getElementById('testEmailModal');
   if (modal) {
      // Use Bootstrap modal if available
      if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
         var bsModal = new bootstrap.Modal(modal);
         bsModal.show();
      } else if (typeof $ !== 'undefined' && $.fn.modal) {
         $(modal).modal('show');
      } else {
         modal.style.display = 'block';
         modal.classList.add('show');
      }
   }
}

// Handle test email form submission
document.addEventListener('submit', function(e) {
   if (e.target && e.target.id === 'testEmailForm') {
      e.preventDefault();

      var form = e.target;
      var formData = new FormData(form);
      var submitBtn = document.getElementById('sendTestEmailBtn');
      var alertDiv = document.getElementById('testEmailAlert');

      if (!submitBtn || !alertDiv) return;

      var originalText = submitBtn.innerHTML;

      // Disable button and show loading
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

      // Hide previous alerts
      alertDiv.style.display = 'none';
      alertDiv.className = 'alert';

      // Send fetch request
      fetch('<?= base_url("admin/settings/test-email") ?>', {
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
         alertDiv.className = 'alert ' + (data.success ? 'alert-success' : 'alert-danger');
         alertDiv.innerHTML = '<i class="fas fa-' + (data.success ? 'check-circle' : 'exclamation-triangle') + '"></i> ' + data.message;
         alertDiv.style.display = 'block';

         if (data.success) {
            // Clear form on success
            form.reset();
         }
      })
      .catch(function(error) {
         console.error('Error:', error);
         alertDiv.className = 'alert alert-danger';
         alertDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> An error occurred while sending the test email.';
         alertDiv.style.display = 'block';
      })
      .finally(function() {
         // Re-enable button
         submitBtn.disabled = false;
         submitBtn.innerHTML = originalText;
      });
   }
});

// Handle service location form submission
document.addEventListener('submit', function(e) {
    if (e.target && e.target.id === 'serviceLocationForm') {
       e.preventDefault();

       var form = e.target;
       var formData = new FormData(form);
       var submitBtn = document.getElementById('saveServiceLocationBtn');

       if (!submitBtn) return;

       var originalText = submitBtn.innerHTML;

       // Disable button and show loading
       submitBtn.disabled = true;
       submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

       // Send fetch request
       fetch('<?= base_url("admin/settings/update") ?>', {
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
             // Show success message
             var alertDiv = document.createElement('div');
             alertDiv.className = 'alert alert-success alert-dismissible mt-3';
             alertDiv.innerHTML = '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                                 '<h5><i class="icon fas fa-check"></i> Success!</h5>' +
                                 data.message;

             // Insert alert before the form
             form.parentNode.insertBefore(alertDiv, form);

             // Auto-hide alert after 5 seconds
             setTimeout(function() {
                if (alertDiv.parentNode) {
                   alertDiv.parentNode.removeChild(alertDiv);
                }
             }, 5000);
          } else {
             // Handle validation errors
             if (data.errors) {
                // Clear previous error messages
                var errorElements = form.querySelectorAll('.text-danger');
                errorElements.forEach(function(el) {
                   el.parentNode.removeChild(el);
                });

                // Show validation errors under each field
                for (var field in data.errors) {
                   var inputElement = form.querySelector('[name="' + field + '"]');
                   if (inputElement) {
                      var errorDiv = document.createElement('div');
                      errorDiv.className = 'text-danger';
                      errorDiv.innerHTML = '<small>' + data.errors[field] + '</small>';
                      inputElement.parentNode.insertBefore(errorDiv, inputElement.nextSibling);
                   }
                }
             } else {
                // Show general error message
                var alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible mt-3';
                alertDiv.innerHTML = '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                                    '<h5><i class="icon fas fa-ban"></i> Error!</h5>' +
                                    data.message;

                form.parentNode.insertBefore(alertDiv, form);
             }
          }
       })
       .catch(function(error) {
          console.error('Error:', error);
          var alertDiv = document.createElement('div');
          alertDiv.className = 'alert alert-danger alert-dismissible mt-3';
          alertDiv.innerHTML = '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                              '<h5><i class="icon fas fa-ban"></i> Error!</h5>' +
                              'An error occurred while saving the settings.';

          form.parentNode.insertBefore(alertDiv, form);
       })
       .finally(function() {
          // Re-enable button
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
       });
    }
});

function resetServiceLocationForm() {
    var form = document.getElementById('serviceLocationForm');
    if (form) {
       form.reset();
    }
}

// Clear form when modal is closed
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('testEmailModal');
    if (modal) {
       modal.addEventListener('hidden.bs.modal', function() {
          var form = document.getElementById('testEmailForm');
          var alertDiv = document.getElementById('testEmailAlert');
          if (form) form.reset();
          if (alertDiv) alertDiv.style.display = 'none';
       });
    }
});
</script>
<?= $this->endSection() ?>