<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-12 col-lg-8">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title mb-0">Edit Charges</h3>
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

          <form action="<?= base_url('admin/charges/update/' . $charge['id']) ?>" method="post">
            <div class="form-group">
              <label for="late_charge">Late Charge</label>
              <input type="number" step="0.01" name="late_charge" id="late_charge" class="form-control" value="<?= old('late_charge', $charge['late_charge']) ?>" required>
            </div>
            <div class="form-group">
              <label for="average_charge">Average Charge</label>
              <input type="number" step="0.01" name="average_charge" id="average_charge" class="form-control" value="<?= old('average_charge', $charge['average_charge']) ?>" required>
            </div>
            <div class="form-group">
              <label for="bounce_charge">Bounce Charge</label>
              <input type="number" step="0.01" name="bounce_charge" id="bounce_charge" class="form-control" value="<?= old('bounce_charge', $charge['bounce_charge']) ?>" required>
            </div>
            <div class="form-group">
              <label for="no_of_days">No Of Days</label>
              <input type="number" min="0" step="1" name="no_of_days" id="no_of_days" class="form-control" value="<?= old('no_of_days', $charge['no_of_days']) ?>" required>
            </div>
            <div class="form-group">
              <label for="annual_charges">Annual Charges</label>
              <input type="number" step="0.01" name="annual_charges" id="annual_charges" class="form-control" value="<?= old('annual_charges', $charge['annual_charges']) ?>" required>
            </div>
            <div class="form-group">
              <label for="minimum_charges">Minimum Charges</label>
              <input type="number" step="0.01" name="minimum_charges" id="minimum_charges" class="form-control" value="<?= old('minimum_charges', $charge['minimum_charges']) ?>" required>
            </div>

            <div class="d-flex justify-content-between">
              <a href="<?= base_url('admin/charges') ?>" class="btn btn-secondary">Back</a>
              <button type="submit" class="btn btn-primary">Update</button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>