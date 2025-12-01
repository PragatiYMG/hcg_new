<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Area</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('admin/areas') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Areas
                        </a>
                    </div>
                </div>
                <form action="<?= base_url('admin/areas/store') ?>" method="post">
                    <div class="card-body">
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h5><i class="icon fas fa-ban"></i> Error!</h5>
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($errors) && !empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('warning')): ?>
                            <div class="alert alert-warning alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h5><i class="icon fas fa-exclamation-triangle"></i> Warning!</h5>
                                <?= session()->getFlashdata('warning') ?>
                                <br><small>If you want to proceed anyway, click "Create Area" again.</small>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="area_name">Area Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['area_name']) ? 'is-invalid' : '' ?>" id="area_name" name="area_name" value="<?= old('area_name') ?>" required>
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
                                <option value="active" <?= old('status') == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                            <?php if (isset($errors['status'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['status'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="visible_to_customer">Visible to Customer <span class="text-danger">*</span></label>
                            <select class="form-control <?= isset($errors['visible_to_customer']) ? 'is-invalid' : '' ?>" id="visible_to_customer" name="visible_to_customer" required>
                                <option value="">Select Visibility</option>
                                <option value="yes" <?= old('visible_to_customer') == 'yes' ? 'selected' : '' ?>>Yes</option>
                                <option value="no" <?= old('visible_to_customer') == 'no' ? 'selected' : '' ?>>No</option>
                            </select>
                            <?php if (isset($errors['visible_to_customer'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['visible_to_customer'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Area
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