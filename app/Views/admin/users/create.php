<?= $this->extend('admin/layout') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?= $this->section('content') ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-12 col-lg-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title mb-0">Create User</h3>
        </div>
        <div class="card-body">
          <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $e): ?>
                  <li><?= esc($e) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <form action="<?= base_url('admin/users/store') ?>" method="post" enctype="multipart/form-data">
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="first_name">First Name</label>
                <input type="text" name="first_name" id="first_name" class="form-control" value="<?= old('first_name') ?>">
              </div>
              <div class="form-group col-md-4">
                <label for="middle_name">Middle Name</label>
                <input type="text" name="middle_name" id="middle_name" class="form-control" value="<?= old('middle_name') ?>">
              </div>
              <div class="form-group col-md-4">
                <label for="last_name">Last Name</label>
                <input type="text" name="last_name" id="last_name" class="form-control" value="<?= old('last_name') ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-3">
                <label for="gender">Gender</label>
                <?php $g = old('gender'); ?>
                <select name="gender" id="gender" class="form-control">
                  <option value="">Select Gender</option>
                  <option value="male" <?= $g==='male'?'selected':'' ?>>Male</option>
                  <option value="female" <?= $g==='female'?'selected':'' ?>>Female</option>
                  <option value="company" <?= $g==='company'?'selected':'' ?>>Company</option>
                </select>
              </div>
              <div class="form-group col-md-3">
                <label for="dob">Date of Birth</label>
                <input type="date" name="dob" id="dob" class="form-control" value="<?= old('dob') ?>">
              </div>
              <div class="form-group col-md-6">
                <label for="father_husband_name">Father/Husband Name</label>
                <input type="text" name="father_husband_name" id="father_husband_name" class="form-control" value="<?= old('father_husband_name') ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="mobile">Mobile</label>
                <input type="text" name="mobile" id="mobile" class="form-control" value="<?= old('mobile') ?>">
              </div>
              <div class="form-group col-md-4">
                <label for="phone_office">Telephone Office Number</label>
                <input type="text" name="phone_office" id="phone_office" class="form-control" value="<?= old('phone_office') ?>">
              </div>
              <div class="form-group col-md-4">
                <label for="phone_residence">Telephone Residence Number</label>
                <input type="text" name="phone_residence" id="phone_residence" class="form-control" value="<?= old('phone_residence') ?>">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= old('email') ?>" required>
              </div>
              <div class="form-group col-md-6">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-3">
                <label for="house_no">House No</label>
                <input type="text" name="house_no" id="house_no" class="form-control" value="<?= old('house_no') ?>">
              </div>
              <div class="form-group col-md-3">
                <label for="block_no">Block No</label>
                <input type="text" name="block_no" id="block_no" class="form-control" value="<?= old('block_no') ?>">
              </div>
              <div class="form-group col-md-3">
                <label for="plot_no">Plot No</label>
                <input type="text" name="plot_no" id="plot_no" class="form-control" value="<?= old('plot_no') ?>">
              </div>
              <div class="form-group col-md-3">
                <label for="sector">Sector</label>
                <input type="text" name="sector" id="sector" class="form-control" value="<?= old('sector') ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="street_name">Street Name</label>
                <input type="text" name="street_name" id="street_name" class="form-control" value="<?= old('street_name') ?>">
              </div>
              <div class="form-group col-md-4">
                <label for="society">Society</label>
                <select name="society" id="society" class="form-control">
                  <option value="">Select Society</option>
                  <?php if (!empty($societies)): foreach ($societies as $s): ?>
                    <option value="<?= esc($s['society_name']) ?>" <?= old('society') === ($s['society_name'] ?? '') ? 'selected' : '' ?>>
                      <?= esc($s['society_name']) ?>
                    </option>
                  <?php endforeach; endif; ?>
                </select>
              </div>
              <div class="form-group col-md-4">
                <label for="landmark">Landmark</label>
                <input type="text" name="landmark" id="landmark" class="form-control" value="<?= old('landmark') ?>">
              </div>
            </div>

            <div class="form-row">             
              <div class="form-group col-md-3">
                <label for="country">Country</label>
                <select name="country_id" id="country" class="form-control">
                  <option value="">Select Country</option>
                  <?php if (!empty($countries)): foreach ($countries as $co): ?>
                      <option value="<?= esc($co['id']) ?>" <?= old('country_id') == $co['id'] ? 'selected' : '' ?>>
                          <?= esc($co['name']) ?>
                      </option>
                  <?php endforeach; endif; ?>
                </select>
              </div>           
              <div class="form-group col-md-3">
                <label for="state">State</label>
                <select name="state" id="state" class="form-control">
                  <option value="">Select State</option>
                </select>
              </div>              
              <div class="form-group col-md-3">
                <label for="city">City</label>
                <select name="city" id="city" class="form-control">
                  <option value="">Select City</option>                  
                </select>
              </div>
              <div class="form-group col-md-3">
                <label for="pincode">Pincode</label>
                <input type="text" name="pincode" id="pincode" class="form-control" value="<?= old('pincode') ?>">
              </div>   
            </div>

            <div class="form-row">              
              <div class="form-group col-md-3">
                <label for="payment_mode">Payment Mode</label>
                <input type="text" name="payment_mode" id="payment_mode" class="form-control" value="<?= old('payment_mode') ?>">
              </div>
              <div class="form-group col-md-3">
                <label for="registration_fee">Registration Fee</label>
                <input type="number" step="0.01" name="registration_fee" id="registration_fee" class="form-control" value="<?= old('registration_fee') ?>">
              </div>
              <div class="form-group col-md-3">
                <label for="accommodation_type">Type of Accommodation</label>
                <input type="text" name="accommodation_type" id="accommodation_type" class="form-control" value="<?= old('accommodation_type') ?>">
              </div>
              <div class="form-group col-md-3">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                  <option value="active" <?= old('status') === 'active' ? 'selected' : '' ?>>Active</option>
                  <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="primary_id_type">Primary ID Type</label>
                <select name="primary_id_type" id="primary_id_type" class="form-control">
                  <option value="">Select ID Type</option>
                  <option value="Voter ID" <?= old('primary_id_type') === 'Voter ID' ? 'selected' : '' ?>>Voter ID</option>
                  <option value="Driving License" <?= old('primary_id_type') === 'Driving License' ? 'selected' : '' ?>>Driving License</option>
                  <option value="Passport" <?= old('primary_id_type') === 'Passport' ? 'selected' : '' ?>>Passport</option>
                  <option value="PAN Card" <?= old('primary_id_type') === 'PAN Card' ? 'selected' : '' ?>>PAN Card</option>
                </select>
              </div>
              <div class="form-group col-md-4">
                <label for="aadhaar_no">Aadhaar Card No.</label>
                <input type="text" name="aadhaar_no" id="aadhaar_no" class="form-control" value="<?= old('aadhaar_no') ?>">
              </div>
              
              <div class="form-group col-md-4">
                <label for="primary_id_no">Primary ID No.</label>
                <input type="text" name="primary_id_no" id="primary_id_no" class="form-control" value="<?= old('primary_id_no') ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="primary_id_file">Primary ID File (JPG/PNG/PDF, max 2MB)</label>
                <input type="file" name="primary_id_file" id="primary_id_file" class="form-control-file">
                <?php if (isset($validation) && $validation->hasError('primary_id_file')): ?>
                  <small class="text-danger"><?= $validation->getError('primary_id_file') ?></small>
                <?php endif; ?>
              </div>
              <div class="form-group col-md-4">
                <label for="secondary_id_type">Secondary ID Type</label>
                <select name="secondary_id_type" id="secondary_id_type" class="form-control">
                  <option value="">Select ID Type</option>
                  <option value="Voter ID" <?= old('secondary_id_type') === 'Voter ID' ? 'selected' : '' ?>>Voter ID</option>
                  <option value="Driving License" <?= old('secondary_id_type') === 'Driving License' ? 'selected' : '' ?>>Driving License</option>
                  <option value="Passport" <?= old('secondary_id_type') === 'Passport' ? 'selected' : '' ?>>Passport</option>
                  <option value="PAN Card" <?= old('secondary_id_type') === 'PAN Card' ? 'selected' : '' ?>>PAN Card</option>
                </select>
              </div>
              <div class="form-group col-md-4">
                <label for="secondary_id_no">Secondary ID No.</label>
                <input type="text" name="secondary_id_no" id="secondary_id_no" class="form-control" value="<?= old('secondary_id_no') ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="secondary_id_file">Secondary ID File (JPG/PNG/PDF, max 2MB)</label>
                <input type="file" name="secondary_id_file" id="secondary_id_file" class="form-control-file">
                <?php if (isset($validation) && $validation->hasError('secondary_id_file')): ?>
                  <small class="text-danger"><?= $validation->getError('secondary_id_file') ?></small>
                <?php endif; ?>
              </div>
              <div class="form-group col-md-4">
                <label for="aadhaar_file">Aadhaar Card File (JPG/PNG/PDF, max 2MB)</label>
                <input type="file" name="aadhaar_file" id="aadhaar_file" class="form-control-file">
                <?php if (isset($validation) && $validation->hasError('aadhaar_file')): ?>
                  <small class="text-danger"><?= $validation->getError('aadhaar_file') ?></small>
                <?php endif; ?>
              </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
              <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Back</a>
              <button type="submit" class="btn btn-primary">Save</button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Handle country change
    $('#country').on('change', function() { 
        var countryId   = $(this).val(); 
        var stateSelect = $('#state');
        
        stateSelect.empty().append('<option value="">Select State</option>');
        
        if (countryId) { 
          $.get('<?= base_url('admin/states/getByCountry/') ?>' + countryId, function(data) {
            if (data && data.length > 0) { 
              $.each(data, function(index, state) {
                stateSelect.append('<option value="' + state.id + '">' + state.name + '</option>');
              });
            }
          }, 'json');          
        }
    });

    // Handle state change
    $('#state').on('change', function() {
        var stateId = $(this).val();
        var citySelect = $('#city');
        
        citySelect.empty().append('<option value="">Select City</option>');
        
        if (stateId) {
            $.ajax({
                url: '<?= base_url('admin/cities/getByState/') ?>' + stateId,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data && data.length > 0) { 
                        $.each(data, function(index, city) {
                            citySelect.append('<option value="' + city.name + '">' + city.name + '</option>');
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading cities:', status, error);
                }
            });
        }
    });
    
    // Trigger change on page load if a country is already selected
    if ($('#country').val()) {
        $('#country').trigger('change');
    }
});
</script>
<?= $this->endSection() ?>

<!-- // $(document).ready(function() {
//     // Handle country change
    
    
//     // Trigger change on page load if a country is already selected
//     if ($('#country').val()) {
//         $('#country').trigger('change');
//     }
// }); -->