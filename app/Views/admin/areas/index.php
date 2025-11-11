<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header row">
                    <h3 class="card-title col-6">Areas Management</h3>
                    <div class="card-tools col-6 text-right">
                        <a href="<?= base_url('admin/areas/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Area
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-check"></i> Success!</h5>
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-ban"></i> Error!</h5>
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <table class="table table-bordered table-striped datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Area Name</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($areas)): ?>
                                <?php foreach ($areas as $area): ?>
                                    <tr>
                                        <td><?= $area['id'] ?></td>
                                        <td><?= esc($area['area_name']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $area['status'] == 'active' ? 'success' : 'danger' ?>">
                                                <?= ucfirst($area['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= $area['created_date'] ? date('d M Y H:i', strtotime($area['created_date'])) : 'N/A' ?></td>
                                        <td>
                                            <a href="<?= base_url('admin/areas/edit/' . $area['id']) ?>" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="<?= base_url('admin/areas/delete/' . $area['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this area?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No areas found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>