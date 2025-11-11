<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
      <?php endif; ?>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Users</h3>
        <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary">Create User</a>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-bordered datatable">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Email</th>
                  <th>Username</th>
                  <th>Active</th>
                  <th>Status</th>
                  <th>Last Active</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($users)): ?>
                  <?php foreach ($users as $u): ?>
                    <tr>
                      <td><?= esc($u['id']) ?></td>
                      <td><?= esc($u['email']) ?></td>
                      <td><?= esc($u['username']) ?></td>
                      <td><span class="badge badge-<?= (int)$u['active'] === 1 ? 'success' : 'secondary' ?>"><?= (int)$u['active'] === 1 ? 'Yes' : 'No' ?></span></td>
                      <td><span class="badge badge-<?= $u['status'] === 'active' ? 'success' : 'secondary' ?>"><?= esc(ucfirst($u['status'])) ?></span></td>
                      <td><?= esc($u['last_active'] ?: '-') ?></td>
                      <td>
                        <a href="<?= base_url('admin/users/edit/' . $u['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <a href="<?= base_url('admin/users/delete/' . $u['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this user?')"><i class="fas fa-trash"></i></a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<?= $this->endSection() ?>