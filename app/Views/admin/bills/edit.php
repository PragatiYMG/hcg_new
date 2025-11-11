<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-12 col-lg-9">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title mb-0">Edit Bill</h3>
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

          <form action="<?= base_url('admin/bills/update/' . $bill['id']) ?>" method="post">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= esc(old('name') !== null ? old('name') : ($bill['name'] ?? '')) ?>" required>
              </div>
              <div class="form-group col-md-6">
                <label for="tag_line">Tag line</label>
                <input type="text" name="tag_line" id="tag_line" class="form-control" value="<?= esc(old('tag_line') !== null ? old('tag_line') : ($bill['tag_line'] ?? '')) ?>">
              </div>
            </div>

            <div class="form-group">
              <label for="address">Address</label>
              <textarea name="address" id="address" class="form-control" rows="2"><?= esc(old('address') !== null ? old('address') : ($bill['address'] ?? '')) ?></textarea>
            </div>

            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="phone">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control" value="<?= esc(old('phone') !== null ? old('phone') : ($bill['phone'] ?? '')) ?>">
              </div>
              <div class="form-group col-md-4">
                <label for="emergency_no">Emergency No.</label>
                <input type="text" name="emergency_no" id="emergency_no" class="form-control" value="<?= esc(old('emergency_no') !== null ? old('emergency_no') : ($bill['emergency_no'] ?? '')) ?>">
              </div>
              <div class="form-group col-md-4">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= esc(old('email') !== null ? old('email') : ($bill['email'] ?? '')) ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="tin">Tin</label>
                <input type="text" name="tin" id="tin" class="form-control" value="<?= esc(old('tin') !== null ? old('tin') : ($bill['tin'] ?? '')) ?>">
              </div>
              <div class="form-group col-md-4">
                <label for="website">Website</label>
                <input type="url" name="website" id="website" class="form-control" value="<?= esc(old('website') !== null ? old('website') : ($bill['website'] ?? '')) ?>">
              </div>
              <div class="form-group col-md-4">
                <label for="active">Active</label>
                <?php $activeVal = old('active') !== null ? old('active') : (string)(int)($bill['active'] ?? 1); ?>
                <select name="active" id="active" class="form-control" required>
                  <option value="1" <?= $activeVal === '1' ? 'selected' : '' ?>>Yes</option>
                  <option value="0" <?= $activeVal === '0' ? 'selected' : '' ?>>No</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="summary_description">Summary Description</label>
              <textarea name="summary_description" id="summary_description" class="form-control" rows="3"><?= esc(old('summary_description') !== null ? old('summary_description') : ($bill['summary_description'] ?? '')) ?></textarea>
            </div>

            <div class="form-group">
              <label for="footer_description">Footer Description</label>
              <textarea name="footer_description" id="footer_description" class="form-control" rows="3"><?= esc(old('footer_description') !== null ? old('footer_description') : ($bill['footer_description'] ?? '')) ?></textarea>
            </div>

            <div class="d-flex justify-content-between">
              <a href="<?= base_url('admin/bills') ?>" class="btn btn-secondary">Back</a>
              <button type="submit" class="btn btn-primary">Update</button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>