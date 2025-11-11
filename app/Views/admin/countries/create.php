<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title mb-0">Add Country</h3>
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
      <form action="<?= base_url('admin/countries/store') ?>" method="post">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="<?= old('name') ?>" required>
          </div>
          <div class="form-group col-md-3">
            <label for="code">Code</label>
            <input type="text" name="code" id="code" class="form-control" value="<?= old('code') ?>" required>
          </div>
          <div class="form-group col-md-3">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control" required>
              <option value="active" <?= old('status')==='active'?'selected':'' ?>>Active</option>
              <option value="inactive" <?= old('status')==='inactive'?'selected':'' ?>>Inactive</option>
            </select>
          </div>
        </div>
        <div class="d-flex justify-content-between">
          <a href="<?= base_url('admin/countries') ?>" class="btn btn-secondary">Back</a>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?= $this->endSection() ?>