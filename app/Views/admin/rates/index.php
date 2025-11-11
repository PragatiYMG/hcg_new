<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="card-title mb-0">Rates</h3>
          <a href="<?= base_url('admin/rates/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Rate
          </a>
        </div>
        <div class="card-body">
          <?php if (session()->getFlashdata('success')): ?>
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= session()->getFlashdata('success') ?>
          </div>
          <?php endif; ?>
          <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= session()->getFlashdata('error') ?>
          </div>
          <?php endif; ?>

          <div class="table-responsive">
            <table class="table table-striped table-bordered datatable">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Basic Rate</th>
                  <th>Rate</th>
                  <th>Effective Date</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($rates)): ?>
                  <?php foreach ($rates as $i => $r): ?>
                  <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= number_format((float)$r['basic_rate'], 2) ?></td>
                    <td><?= number_format((float)$r['rate'], 2) ?></td>
                    <td><?= esc($r['effective_date']) ?></td>
                    <td>
                      <span class="badge badge-<?= $r['status'] === 'active' ? 'success' : 'secondary' ?>">
                        <?= ucfirst($r['status']) ?>
                      </span>
                    </td>
                    <td><?= !empty($r['created_date']) ? date('Y-m-d H:i', strtotime($r['created_date'])) : '-' ?></td>
                    <td>
                      <a href="<?= base_url('admin/rates/edit/' . $r['id']) ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit"></i>
                      </a>
                      <a href="<?= base_url('admin/rates/delete/' . $r['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this rate?')">
                        <i class="fas fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="7" class="text-center">No rates found.</td></tr>
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