<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title mb-0">Edit State</h3>
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
      <form action="<?= base_url('admin/states/update/'.$state['id']) ?>" method="post">
        <div class="form-row">
          <div class="form-group col-md-5">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="<?= esc(old('name') ?? $state['name']) ?>" required>
          </div>
          <div class="form-group col-md-3">
            <label for="code">Code</label>
            <input type="text" name="code" id="code" class="form-control" value="<?= esc(old('code') ?? $state['code']) ?>" required>
          </div>
          <div class="form-group col-md-4">
            <label for="country_id">Country</label>
            <?php $cid = old('country_id') ?? ($state['country_id'] ?? ''); ?>
            <select name="country_id" id="country_id" class="form-control" required>
              <option value="">Select Country</option>
              <?php if (!empty($countries)): foreach ($countries as $c): ?>
                <option value="<?= esc($c['id']) ?>" <?= (string)$cid === (string)($c['id'] ?? '') ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
              <?php endforeach; endif; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="status">Status</label>
          <?php $statusVal = old('status') ?? ($state['status'] ?? 'active'); ?>
          <select name="status" id="status" class="form-control" required>
            <option value="active" <?= $statusVal==='active'?'selected':'' ?>>Active</option>
            <option value="inactive" <?= $statusVal==='inactive'?'selected':'' ?>>Inactive</option>
          </select>
        </div>
        <div class="d-flex justify-content-between">
          <a href="<?= base_url('admin/states') ?>" class="btn btn-secondary">Back</a>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?= $this->endSection() ?>