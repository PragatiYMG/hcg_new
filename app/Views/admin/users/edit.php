<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-12 col-lg-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title mb-0">Edit User</h3>
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

          <form action="<?= base_url('admin/users/update/' . $user['id']) ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
              <label for="username">Username</label>
              <input type="text" id="username" class="form-control" value="<?= esc($user['username']) ?>" readonly>
            </div>

            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="first_name">First Name</label>
                <input type="text" name="first_name" id="first_name" class="form-control" value="<?= esc(old('first_name') ?? ($user['first_name'] ?? '')) ?>">
              </div>
              <div class="form-group col-md-4">
                <label for="middle_name">Middle Name</label>
                <input type="text" name="middle_name" id="middle_name" class="form-control" value="<?= esc(old('middle_name') ?? ($user['middle_name'] ?? '')) ?>">
              </div>
              <div class="form-group col-md-4">
                <label for="last_name">Last Name</label>
                <input type="text" name="last_name" id="last_name" class="form-control" value="<?= esc(old('last_name') ?? ($user['last_name'] ?? '')) ?>">
              </div>
            </div>

            <div class="form-row">
              <?php $g = old('gender') ?? ($user['gender'] ?? ''); ?>
              <div class="form-group col-md-3">
                <label for="gender">Gender</label>
                <select name="gender" id="gender" class="form-control">
                  <option value="">Select Gender</option>
                  <option value="male" <?= $g==='male'?'selected':'' ?>>Male</option>
                  <option value="female" <?= $g==='female'?'selected':'' ?>>Female</option>
                  <option value="company" <?= $g==='company'?'selected':'' ?>>Company</option>
                </select>
              </div>
              <div class="form-group col-md-3">
                <label for="dob">Date of Birth</label>
                <input type="date" name="dob" id="dob" class="form-control" value="<?= esc(old('dob') ?? ($user['dob'] ?? '')) ?>">
              </div>
              <div class="form-group col-md-6">
                <label for="father_husband_name">Father/Husband Name</label>
                <input type="text" name="father_husband_name" id="father_husband_name" class="form-control" value="<?= esc(old('father_husband_name') ?? ($user['father_husband_name'] ?? '')) ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="mobile">Mobile</label>
                <input type="text" name="mobile" id="mobile" class="form-control" value="<?= esc(old('mobile') ?? ($user['mobile'] ?? '')) ?>">
              </div>
              <div class="form-group col-md-4">
                <label for="phone_office">Telephone Office Number</label>
                <input type="text" name="phone_office" id="phone_office" class="form-control" value="<?= esc(old('phone_office') ?? ($user['phone_office'] ?? '')) ?>">
              </div>
              <div class="form-group col-md-4">
                <label for="phone_residence">Telephone Residence Number</label>
                <input type="text" name="phone_residence" id="phone_residence" class="form-control" value="<?= esc(old('phone_residence') ?? ($user['phone_residence'] ?? '')) ?>">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= esc(old('email') ?? ($user['email'] ?? '')) ?>" required>
              </div>
              <div class="form-group col-md-6">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Leave blank to keep current">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-3">
                <label for="house_no">House No</label>
                <input type="text" name="house_no" id="house_no" class="form-control" value="<?= esc(old('house_no') ?? ($user['house_no'] ?? '')) ?>">
              </div>
              <div class="form-group col-md-3">
                <label for="block_no">Block No</label>
                <input type="text" name="block_no" id="block_no" class="form-control" value="<?= esc(old('block_no') ?? ($user['block_no'] ?? '')) ?>">
              </div>
              <div class="form-group col-md-3">
                <label for="plot_no">Plot No</label>
                <input type="text" name="plot_no" id="plot_no" class="form-control" value="<?= esc(old('plot_no') ?? ($user['plot_no'] ?? '')) ?>">
              </div>
              <div class="form-group col-md-3">
                <label for="sector">Sector</label>
                <input type="text" name="sector" id="sector" class="form-control" value="<?= esc(old('sector') ?? ($user['sector'] ?? '')) ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="street_name">Street Name</label>
                <input type="text" name="street_name" id="street_name" class="form-control" value="<?= esc(old('street_name') ?? ($user['street_name'] ?? '')) ?>">
              </div>
              <div class="form-group col-md-4">
                <label for="society">Society</label>
                <select name="society" id="society" class="form-control">
                  <option value="">Select Society</option>
                  <?php if (!empty($societies)): foreach ($societies as $s): ?>
                    <?php $val = $s['society_name'] ?? ''; $sel = (old('society') !== null ? old('society') : ($user['society'] ?? '')) === $val ? 'selected' : ''; ?>
                    <option value="<?= esc($val) ?>" <?= $sel ?>><?= esc($s['society_name']) ?></option>
                  <?php endforeach; endif; ?>
                </select>
              </div>
              <div class="form-group col-md-4">
                <label for="landmark">Landmark</label>
                <input type="text" name="landmark" id="landmark" class="form-control" value="<?= esc(old('landmark') ?? ($user['landmark'] ?? '')) ?>">
              </div>
            </div>

            <div class="form-row">              
              <div class="form-group col-md-3">
                <label for="country">Country</label>
                <?php $countryVal = old('country') !== null ? old('country') : ($user['country'] ?? ''); ?>
                <select name="country" id="country" class="form-control">
                  <option value="">Select Country</option>
                  <?php if (!empty($countries)): foreach ($countries as $co): ?>
                    <?php $val = $co['name'] ?? ''; ?>
                    <option value="<?= esc($val) ?>" <?= $countryVal === $val ? 'selected' : '' ?>><?= esc($co['name'] ?? '') ?></option>
                  <?php endforeach; endif; ?>
                </select>
              </div>
              <div class="form-group col-md-3">
                <label for="state">State</label>
                <?php $stateVal = old('state') !== null ? old('state') : ($user['state'] ?? ''); ?>
                <select name="state" id="state" class="form-control">
                  <option value="">Select State</option>
                  <?php if (!empty($states)): foreach ($states as $st): ?>
                    <?php $val = $st['name'] ?? ''; ?>
                    <option value="<?= esc($val) ?>" <?= $stateVal === $val ? 'selected' : '' ?>><?= esc($st['name'] ?? '') ?></option>
                  <?php endforeach; endif; ?>
                </select>
              </div>
              <div class="form-group col-md-3">
                <label for="city">City</label>
                <?php $cityVal = old('city') !== null ? old('city') : ($user['city'] ?? ''); ?>
                <select name="city" id="city" class="form-control">
                  <option value="">Select City</option>
                  <?php if (!empty($cities)): foreach ($cities as $ci): ?>
                    <?php $val = $ci['name'] ?? ''; ?>
                    <option value="<?= esc($val) ?>" <?= $cityVal === $val ? 'selected' : '' ?>><?= esc($ci['name'] ?? '') ?></option>
                  <?php endforeach; endif; ?>
                </select>
              </div>
              <div class="form-group col-md-3">
                <label for="pincode">Pincode</label>
                <input type="text" name="pincode" id="pincode" class="form-control" value="<?= esc(old('pincode') ?? ($user['pincode'] ?? '')) ?>">
              </div>
            </div>

            <div class="form-row">              
              <div class="form-group col-md-6">
                <label for="payment_mode">Payment Mode</label>
                <input type="text" name="payment_mode" id="payment_mode" class="form-control" value="<?= esc(old('payment_mode') ?? ($user['payment_mode'] ?? '')) ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="registration_fee">Registration Fee</label>
                <input type="number" step="0.01" name="registration_fee" id="registration_fee" class="form-control" value="<?= esc(old('registration_fee') ?? ($user['registration_fee'] ?? '')) ?>">
              </div>
              <div class="form-group col-md-6">
                <label for="accommodation_type">Type of Accommodation</label>
                <input type="text" name="accommodation_type" id="accommodation_type" class="form-control" value="<?= esc(old('accommodation_type') ?? ($user['accommodation_type'] ?? '')) ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="aadhaar_no">Aadhaar No</label>
                <input type="text" name="aadhaar_no" id="aadhaar_no" class="form-control" value="<?= esc(old('aadhaar_no') ?? ($user['aadhaar_no'] ?? '')) ?>">
              </div>
              <div class="form-group col-md-6">
                <label for="aadhaar_file_path">Upload Aadhaar</label>
                <input type="file" name="aadhaar_file_path" id="aadhaar_file_path" class="form-control-file" accept="image/*,application/pdf">
                <?php if (!empty($user['aadhaar_file_path'])): ?>
                  <small>Current: <a href="<?= base_url($user['aadhaar_file_path']) ?>" target="_blank">View</a></small>
                <?php endif; ?>
              </div>
            </div>

            <div class="form-group">
              <label for="photo_path">Your Photo</label>
              <input type="file" name="photo_path" id="photo_path" class="form-control-file" accept="image/*,application/pdf">
              <?php if (!empty($user['photo_path'])): ?>
                <small>Current: <a href="<?= base_url($user['photo_path']) ?>" target="_blank">View</a></small>
              <?php endif; ?>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="primary_id_type">Primary ID Type</label>
                <input type="text" name="primary_id_type" id="primary_id_type" class="form-control" value="<?= esc(old('primary_id_type') ?? ($user['primary_id_type'] ?? '')) ?>">
              </div>
              <div class="form-group col-md-6">
                <label for="primary_id_no">Primary ID No</label>
                <input type="text" name="primary_id_no" id="primary_id_no" class="form-control" value="<?= esc(old('primary_id_no') ?? ($user['primary_id_no'] ?? '')) ?>">
              </div>
            </div>

            <div class="form-group">
              <label for="primary_id_file_path">Upload Primary ID</label>
              <input type="file" name="primary_id_file_path" id="primary_id_file_path" class="form-control-file" accept="image/*,application/pdf">
              <?php if (!empty($user['primary_id_file_path'])): ?>
                <small>Current: <a href="<?= base_url($user['primary_id_file_path']) ?>" target="_blank">View</a></small>
              <?php endif; ?>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="secondary_id_type">Secondary ID Type</label>
                <input type="text" name="secondary_id_type" id="secondary_id_type" class="form-control" value="<?= esc(old('secondary_id_type') ?? ($user['secondary_id_type'] ?? '')) ?>">
              </div>
              <div class="form-group col-md-6">
                <label for="secondary_id_no">Secondary ID No</label>
                <input type="text" name="secondary_id_no" id="secondary_id_no" class="form-control" value="<?= esc(old('secondary_id_no') ?? ($user['secondary_id_no'] ?? '')) ?>">
              </div>
            </div>

            <div class="form-group">
              <label for="secondary_id_file_path">Upload Secondary ID</label>
              <input type="file" name="secondary_id_file_path" id="secondary_id_file_path" class="form-control-file" accept="image/*,application/pdf">
              <?php if (!empty($user['secondary_id_file_path'])): ?>
                <small>Current: <a href="<?= base_url($user['secondary_id_file_path']) ?>" target="_blank">View</a></small>
              <?php endif; ?>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="active">Active</label>
                <?php $activeVal = old('active') !== null ? old('active') : (string)(int)$user['active']; ?>
                <select name="active" id="active" class="form-control" required>
                  <option value="1" <?= $activeVal === '1' ? 'selected' : '' ?>>Yes</option>
                  <option value="0" <?= $activeVal === '0' ? 'selected' : '' ?>>No</option>
                </select>
              </div>
              <div class="form-group col-md-6">
                <label for="status">Status</label>
                <?php $statusVal = old('status') !== null ? old('status') : ($user['status'] ?? 'active'); ?>
                <select name="status" id="status" class="form-control" required>
                  <option value="active" <?= $statusVal === 'active' ? 'selected' : '' ?>>Active</option>
                  <option value="inactive" <?= $statusVal === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
              </div>
            </div>

            <div class="d-flex justify-content-between">
              <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Back</a>
              <button type="submit" class="btn btn-primary">Update</button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>