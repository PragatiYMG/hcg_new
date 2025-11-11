<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Countries</h3>
    <a href="<?= base_url('admin/countries/create') ?>" class="btn btn-primary">Add Country</a>
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
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($countries)): foreach ($countries as $i => $c): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><?= esc($c['name']) ?></td>
                <td><?= esc($c['code']) ?></td>
                <td><span class="badge badge-<?= $c['status']==='active'?'success':'secondary' ?>"><?= esc(ucfirst($c['status'])) ?></span></td>
                <td class="text-nowrap">
                  <a href="<?= base_url('admin/countries/edit/'.$c['id']) ?>" class="btn btn-sm btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                  <a href="<?= base_url('admin/countries/delete/'.$c['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this country?')"><i class="fas fa-trash"></i></a>
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