<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="card-title mb-0">Charges</h3>
          <span class="text-muted small">List & Edit only</span>
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
                  <th>Late Charge</th>
                  <th>Average Charge</th>
                  <th>Bounce Charge</th>
                  <th>No Of Days</th>
                  <th>Annual Charges</th>
                  <th>Minimum Charges</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($charges)): ?>
                  <?php foreach ($charges as $i => $c): ?>
                  <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= number_format((float)$c['late_charge'], 2) ?></td>
                    <td><?= number_format((float)$c['average_charge'], 2) ?></td>
                    <td><?= number_format((float)$c['bounce_charge'], 2) ?></td>
                    <td><?= (int)$c['no_of_days'] ?></td>
                    <td><?= number_format((float)$c['annual_charges'], 2) ?></td>
                    <td><?= number_format((float)$c['minimum_charges'], 2) ?></td>
                    <td><?= !empty($c['created_date']) ? date('Y-m-d H:i', strtotime($c['created_date'])) : '-' ?></td>
                    <td>
                      <a href="<?= base_url('admin/charges/edit/' . $c['id']) ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit"></i>
                      </a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="9" class="text-center">No charges found.</td></tr>
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