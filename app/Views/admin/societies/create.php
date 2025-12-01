<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Add Society</h3>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-ban"></i> Error!</h5>
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $e): ?>
                                    <li><?= esc($e) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/societies/store') ?>" method="post">
                        <div class="form-group">
                            <label for="area_id">Area <span class="text-danger">*</span></label>
                            <select name="area_id" id="area_id" class="form-control" required>
                                <option value="">Select Area</option>
                                <?php if (!empty($areas)): ?>
                                    <?php foreach ($areas as $area): ?>
                                        <option value="<?= $area['id'] ?>" <?= old('area_id') == $area['id'] ? 'selected' : '' ?>>
                                            <?= esc($area['area_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="society_name">Society Name</label>
                            <input type="text" name="society_name" id="society_name" class="form-control" value="<?= old('society_name') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="active" <?= old('status') === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="visible_to_customer">Visible to Customer <span class="text-danger">*</span></label>
                            <select name="visible_to_customer" id="visible_to_customer" class="form-control" required>
                                <option value="yes" <?= old('visible_to_customer') === 'yes' ? 'selected' : '' ?>>Yes</option>
                                <option value="no" <?= old('visible_to_customer') === 'no' ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('admin/societies') ?>" class="btn btn-secondary">Back</a>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>