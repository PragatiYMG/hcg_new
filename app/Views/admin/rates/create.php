<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-12 col-lg-8">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title mb-0">Add Rate</h3>
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

          <form action="<?= base_url('admin/rates/store') ?>" method="post">
            <div class="form-group">
              <label for="basic_rate">Basic Rate</label>
              <input type="number" step="0.01" name="basic_rate" id="basic_rate" class="form-control" value="<?= old('basic_rate') ?>" required>
            </div>
            <div class="form-group">
              <label for="rate">Rate</label>
              <input type="number" step="0.01" name="rate" id="rate" class="form-control" value="<?= old('rate') ?>" required>
            </div>
            <div class="form-group">
              <label for="effective_date">Effective Date</label>
              <input type="date" name="effective_date" id="effective_date" class="form-control" value="<?= old('effective_date') ?>" required>
            </div>
            <div class="form-group">
              <label for="status">Status</label>
              <select name="status" id="status" class="form-control" required>
                <option value="active" <?= old('status') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
              </select>
            </div>

            <div class="d-flex justify-content-between">
              <a href="<?= base_url('admin/rates') ?>" class="btn btn-secondary">Back</a>
              <button type="submit" class="btn btn-primary">Save</button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>