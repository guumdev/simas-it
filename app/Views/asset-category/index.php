<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/partials/breadcrumb') ?>

<div class="row">
  <div class="col-12">
    <?= $this->include('asset-category/partials/modal') ?>
    <?= $this->include('asset-category/partials/counter'); ?>

    <div class="card">
      <div class="card-header bg-primary-subtle">
        <div class="d-flex gap-2 justify-content-start">
          <a class="btn btn-primary w-sm waves-effect waves-light btn-create" data-bs-target=".bs-example-modal-lg"><i class="uil-plus me-2"></i>Buat</a>
        </div>
      </div>
      <div class="card-body">
        <table id="asset-category-dtable" class="table table-striped table-bordered" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
          <thead>
            <tr>
              <th style="width: 5%;">No.</th>
              <th>Kode Kategori</th>
              <th>Pengelola Aset</th>
              <th>Nama Kategori</th>
              <th style="width: 15%;">Aksi</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('custom-js') ?>
<?= $this->include('asset-category/scripts/datatables') ?>
<?= $this->include('asset-category/scripts/script') ?>

<?= $this->endSection() ?>