<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Cities</h3>
    <a href="<?= base_url('admin/cities/create') ?>" class="btn btn-primary">Add City</a>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
  <?php elseif (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped datatable" id="dataTable">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Code</th>
              <th>Country</th>
              <th>State</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($cities)): foreach ($cities as $i => $ci): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><?= esc($ci['name']) ?></td>
                <td><?= esc($ci['code']) ?></td>
                <td><?= esc($ci['country_name'] ?? '') ?></td>
                <td><?= esc($ci['state_name'] ?? '') ?></td>
                <td><span class="badge badge-<?= $ci['status']==='active'?'success':'secondary' ?>"><?= esc(ucfirst($ci['status'])) ?></span></td>
                <td class="text-nowrap">
                  <a href="<?= base_url('admin/cities/edit/'.$ci['id']) ?>" class="btn btn-sm btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                  <a href="<?= base_url('admin/cities/delete/'.$ci['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this city?')"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>