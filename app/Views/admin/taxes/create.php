<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Add Tax</h3>
                </div>
                <div class="card-body">
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

                    <form action="<?= base_url('admin/taxes/store') ?>" method="post">
                        <div class="form-group">
                            <label for="tax_type">Tax Type</label>
                            <input type="text" name="tax_type" id="tax_type" class="form-control" value="<?= old('tax_type') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="tax_percentage">Tax Percentage</label>
                            <input type="number" step="0.01" min="0" name="tax_percentage" id="tax_percentage" class="form-control" value="<?= old('tax_percentage') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="tax_description">Description</label>
                            <textarea name="tax_description" id="tax_description" class="form-control" rows="3"><?= old('tax_description') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="active" <?= old('status') === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('admin/taxes') ?>" class="btn btn-secondary">Back</a>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
