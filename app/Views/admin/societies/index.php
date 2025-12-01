<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Societies</h3>
                    <div>
                        <button class="btn btn-outline-secondary btn-sm" type="button" data-toggle="collapse" data-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                            <i class="fas fa-filter"></i> Filters
                        </button>
                        <a href="?export=csv<?= !empty($_GET) ? '&' . http_build_query(array_diff_key($_GET, ['export' => ''])) : '' ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                        <a href="<?= base_url('admin/societies/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Society
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
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Error!</h5>
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <!-- Filter Form -->
                    <div class="collapse" id="filterCollapse">
                        <div class="card card-body mb-3">
                            <form method="GET" class="row g-3">
                                <div class="col-md-2">
                                    <label for="area_id" class="form-label">Area</label>
                                    <select name="area_id" id="area_id" class="form-control form-control-sm">
                                        <option value="">All Areas</option>
                                        <?php if (!empty($areas)): ?>
                                            <?php foreach ($areas as $area): ?>
                                                <option value="<?= $area['id'] ?>" <?= ($filters['area_id'] ?? '') == $area['id'] ? 'selected' : '' ?>>
                                                    <?= esc($area['area_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-control form-control-sm">
                                        <option value="">All Status</option>
                                        <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="visible_to_customer" class="form-label">Visible to Customer</label>
                                    <select name="visible_to_customer" id="visible_to_customer" class="form-control form-control-sm">
                                        <option value="">All</option>
                                        <option value="yes" <?= ($filters['visible_to_customer'] ?? '') === 'yes' ? 'selected' : '' ?>>Yes</option>
                                        <option value="no" <?= ($filters['visible_to_customer'] ?? '') === 'no' ? 'selected' : '' ?>>No</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="created_from" class="form-label">Created From</label>
                                    <input type="date" name="created_from" id="created_from" class="form-control form-control-sm" value="<?= $filters['created_from'] ?? '' ?>">
                                </div>
                                <div class="col-md-2">
                                    <label for="created_to" class="form-label">Created To</label>
                                    <input type="date" name="created_to" id="created_to" class="form-control form-control-sm" value="<?= $filters['created_to'] ?? '' ?>">
                                </div>
                                <div class="col-md-2">
                                    <label for="updated_from" class="form-label">Updated From</label>
                                    <input type="date" name="updated_from" id="updated_from" class="form-control form-control-sm" value="<?= $filters['updated_from'] ?? '' ?>">
                                </div>
                                <div class="col-md-2">
                                    <label for="updated_to" class="form-label">Updated To</label>
                                    <input type="date" name="updated_to" id="updated_to" class="form-control form-control-sm" value="<?= $filters['updated_to'] ?? '' ?>">
                                </div>
                                <div class="col-md-3 d-flex align-items-end justify-content-around">
                                    <button type="submit" class="btn btn-primary btn-sm me-2">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <a href="<?= base_url('admin/societies') ?>" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Society Name</th>
                                    <th>Area</th>
                                    <th>Status</th>
                                    <th>Visible to Customer</th>
                                    <th>Created By</th>
                                    <th>Last Update</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($societies)): ?>
                                    <?php foreach ($societies as $i => $s): ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <td><?= esc($s['society_name']) ?></td>
                                            <td><?= esc($s['area_name'] ?? 'N/A') ?></td>
                                            <td>
                                                <span class="badge badge-<?= $s['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                    <?= ucfirst($s['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= $s['visible_to_customer'] === 'yes' ? 'success' : 'warning' ?>">
                                                    <?= ucfirst($s['visible_to_customer']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small>
                                                    <strong><?= esc($s['created_by_name'] ?? 'Unknown') ?></strong><br>
                                                    <?= !empty($s['created_date']) ? date('d M Y H:i', strtotime($s['created_date'])) : '-' ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php if (!empty($s['updated_at'])): ?>
                                                    <small>
                                                        <strong><?= esc($s['updated_by_name'] ?? 'Unknown') ?></strong><br>
                                                        <?= date('d M Y H:i', strtotime($s['updated_at'])) ?>
                                                    </small>
                                                <?php else: ?>
                                                    <small class="text-muted">Never updated</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('admin/societies/edit/' . $s['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No societies found.</td>
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