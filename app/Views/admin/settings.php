<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
   <div class="row">
      <div class="col-12">
         <div class="card">
            <div class="card-header row">
               <h3 class="card-title col-6">Settings</h3>
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
            <form action="<?= base_url('admin/settings/update') ?>" method="post" enctype="multipart/form-data">
               <div class="form-group row mb-4">
                  <label class="col-md-3 col-form-label">Site Logo</label>
                  <div class="col-md-9">
                     <div class="custom-file">
                        <input type="file" class="custom-file-input" id="logo" name="logo" accept="image/*">
                        <label class="custom-file-label" for="logo">Choose logo file (max 2MB)</label>
                     </div>
                     <small class="form-text text-muted">Recommended size: 180x50px. Formats: JPG, PNG, WebP</small>
                     <?php if (!empty($site_logo) && file_exists(ROOTPATH . 'public/uploads/' . $site_logo)): ?>
                        <div class="mt-2">
                           <p>Current Logo:</p>
                           <img src="<?= base_url('uploads/' . $site_logo) ?>" alt="Current Logo" class="current-image">
                        </div>
                     <?php endif; ?>
                  </div>
               </div>

               <div class="form-group row mb-4">
                  <label class="col-md-3 col-form-label">Favicon</label>
                  <div class="col-md-9">
                     <div class="custom-file">
                        <input type="file" class="custom-file-input" id="favicon" name="favicon" accept=".ico,image/x-icon,image/vnd.microsoft.icon,image/png">
                        <label class="custom-file-label" for="favicon">Choose favicon file (max 1MB)</label>
                     </div>
                     <small class="form-text text-muted">Recommended size: 32x32px or 64x64px. Formats: ICO, PNG, JPG</small>
                     <?php if (!empty($site_favicon) && file_exists(ROOTPATH . 'public/uploads/' . $site_favicon)): ?>
                        <div class="mt-2">
                           <p>Current Favicon:</p>
                           <img src="<?= base_url('uploads/' . $site_favicon) ?>" alt="Current Favicon" class="current-image">
                        </div>
                     <?php endif; ?>
                  </div>
               </div>
               <div class="card-footer text-center">
                  <button type="submit" class="btn btn-primary">
                     <i class="fas fa-save"></i> Update Settings
                  </button>
                  <a href="<?= base_url('admin/settings') ?>" class="btn btn-secondary">
                     Cancel
                  </a>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
<?= $this->endSection() ?>