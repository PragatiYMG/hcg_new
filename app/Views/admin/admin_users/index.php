<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>User Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">User Management</h4>
                    <a href="<?= base_url('admin/admin-users/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New User
                    </a>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered datatable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Profile Picture</th>
                                    <th>Username</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($admins as $admin): ?>
                                    <tr>
                                        <td><?= $admin['id'] ?></td>
                                        <td class="text-center">
                                            <?php if ($admin['profile_picture']): ?>
                                                <img src="<?= base_url('uploads/Admin_Profile/' . $admin['profile_picture']) ?>"
                                                     alt="Profile" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($admin['username']) ?></td>
                                        <td><?= esc($admin['name'] ?? 'N/A') ?></td>
                                        <td><?= esc($admin['email']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $admin['role'] === 'super_admin' ? 'danger' : ($admin['role'] === 'admin' ? 'primary' : 'info') ?>">
                                                <?= ucfirst(str_replace('_', ' ', $admin['role'])) ?>
                                            </span>
                                        </td>
                                        <td><?= $admin['created_at'] ? date('d M Y H:i', strtotime($admin['created_at'])) : 'N/A' ?></td>
                                        <td>
                                            <a href="<?= base_url('admin/admin-users/edit/' . $admin['id']) ?>"
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>