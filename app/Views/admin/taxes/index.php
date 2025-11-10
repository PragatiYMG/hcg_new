<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Tax Master</h3>
                    <a href="<?= base_url('admin/taxes/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Tax
                    </a>
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
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Error!</h5>
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tax Type</th>
                                    <th>Percentage</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($taxes)): ?>
                                    <?php foreach ($taxes as $i => $tax): ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <td><?= esc($tax['tax_type']) ?></td>
                                            <td><?= number_format((float)$tax['tax_percentage'], 2) ?>%</td>
                                            <td>
                                                <span class="badge badge-<?= $tax['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                    <?= ucfirst($tax['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= !empty($tax['created_date']) ? date('Y-m-d H:i', strtotime($tax['created_date'])) : '-' ?></td>
                                            <td>
                                                <a href="<?= base_url('admin/taxes/edit/' . $tax['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?= base_url('admin/taxes/delete/' . $tax['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this tax?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No taxes found.</td>
                                    </tr>
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
