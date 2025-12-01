<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Bank</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('admin/banks') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Banks
                        </a>
                    </div>
                </div>
                <form action="<?= base_url('admin/banks/update/' . $bank['id']) ?>" method="post">
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
                            <label for="bank_name">Bank Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['bank_name']) ? 'is-invalid' : '' ?>" id="bank_name" name="bank_name" value="<?= old('bank_name', $bank['bank_name']) ?>" required>
                            <small class="form-text text-muted">Enter the full name of the bank (e.g., State Bank of India, HDFC Bank)</small>
                            <?php if (isset($errors['bank_name'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['bank_name'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Bank
                        </button>
                        <a href="<?= base_url('admin/banks') ?>" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>