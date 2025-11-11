<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title mb-0">Edit Country</h3>
    </div>
    <div class="card-body">
      <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $e): ?>
              <li><?= esc($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      <form action="<?= base_url('admin/countries/update/'.$country['id']) ?>" method="post">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="<?= esc(old('name') ?? $country['name']) ?>" required>
          </div>
          <div class="form-group col-md-3">
            <label for="code">Code</label>
            <input type="text" name="code" id="code" class="form-control" value="<?= esc(old('code') ?? $country['code']) ?>" required>
          </div>
          <div class="form-group col-md-3">
            <label for="status">Status</label>
            <?php $statusVal = old('status') ?? ($country['status'] ?? 'active'); ?>
            <select name="status" id="status" class="form-control" required>
              <option value="active" <?= $statusVal==='active'?'selected':'' ?>>Active</option>
              <option value="inactive" <?= $statusVal==='inactive'?'selected':'' ?>>Inactive</option>
            </select>
          </div>
        </div>
        <div class="d-flex justify-content-between">
          <a href="<?= base_url('admin/countries') ?>" class="btn btn-secondary">Back</a>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?= $this->endSection() ?>