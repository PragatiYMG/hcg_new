<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Area</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('admin/areas') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Areas
                        </a>
                    </div>
                </div>
                <form action="<?= base_url('admin/areas/update/' . $area['id']) ?>" method="post">
                    <div class="card-body">
                        <?php if (isset($errors) && !empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="area_name">Area Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['area_name']) ? 'is-invalid' : '' ?>" id="area_name" name="area_name" value="<?= old('area_name', $area['area_name']) ?>" required>
                            <?php if (isset($errors['area_name'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['area_name'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control <?= isset($errors['status']) ? 'is-invalid' : '' ?>" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="active" <?= old('status', $area['status']) == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= old('status', $area['status']) == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                            <?php if (isset($errors['status'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['status'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Area
                        </button>
                        <a href="<?= base_url('admin/areas') ?>" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>