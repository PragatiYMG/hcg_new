<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-12 col-lg-8">
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

          <form action="<?= base_url('admin/users/update/' . $user['id']) ?>" method="post">
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" name="email" id="email" class="form-control" value="<?= esc(old('email') !== null ? old('email') : $user['email']) ?>" required>
            </div>
            <div class="form-group">
              <label for="username">Username</label>
              <input type="text" name="username" id="username" class="form-control" value="<?= esc(old('username') !== null ? old('username') : $user['username']) ?>" required>
            </div>
            <div class="form-group">
              <label for="password">Password</label>
              <input type="password" name="password" id="password" class="form-control" placeholder="Leave blank to keep current">
            </div>
            <div class="form-group">
              <label for="active">Active</label>
              <?php $activeVal = old('active') !== null ? old('active') : (string)(int)$user['active']; ?>
              <select name="active" id="active" class="form-control" required>
                <option value="1" <?= $activeVal === '1' ? 'selected' : '' ?>>Yes</option>
                <option value="0" <?= $activeVal === '0' ? 'selected' : '' ?>>No</option>
              </select>
            </div>
            <div class="form-group">
              <label for="status">Status</label>
              <?php $statusVal = old('status') !== null ? old('status') : ($user['status'] ?? 'active'); ?>
              <select name="status" id="status" class="form-control" required>
                <option value="active" <?= $statusVal === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $statusVal === 'inactive' ? 'selected' : '' ?>>Inactive</option>
              </select>
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