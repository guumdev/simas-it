<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/partials/breadcrumb') ?>

<div class="row">
  <div class="col-12">

    <!-- Alert Messages -->
    <?= $this->include('layouts/partials/alert') ?>

    <div class="card">
      <div class="card-header bg-primary-subtle">
        <div class="d-flex gap-2 justify-content-start">
          <div class="col-lg-6">
            <div class="d-flex gap-2">
              <a href="<?= base_url('/asset-movements/create'); ?>" class="btn btn-primary w-sm waves-effect waves-light btn-create">
                <i class="uil-plus me-2"></i>Buat
              </a>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="d-flex justify-content-end gap-2">
              <a href="<?= base_url('/asset-movements/excel/export') ?>" class="btn btn-success w-sm waves-effect waves-light">
                <i class="uil-export me-2"></i>Export
              </a>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row align-items-end g-3 mb-3">
          <div class="col-lg-3">
            <label for="date-start-filter" class="form-label">Tanggal Awal</label>
            <input type="text" class="form-control" id="date-start-filter" placeholder="dd/mm/yyyy">
          </div>
          <div class="col-lg-3">
            <label for="date-end-filter" class="form-label">Tanggal Akhir</label>
            <input type="text" class="form-control" id="date-end-filter" placeholder="dd/mm/yyyy">
          </div>
          <div class="col-lg-6">
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-danger w-sm waves-effect waves-light" id="btn-reset">
                <i class="uil-redo me-2"></i>Reset
              </button>
            </div>
          </div>
        </div>
        <table id="movements-dtable" class="table table-striped table-bordered" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
          <thead>
            <tr>
              <th style="width: 5%;">ID</th>
              <th>Aset</th>
              <th>Lokasi Awal</th>
              <th>Lokasi Akhir</th>
              <th style="width: 13%;">Jenis Perpindahan</th>
              <th>Perpindahan oleh</th>
              <th>Tanggal</th>
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
<?= $this->include('asset-movement/scripts/datatables') ?>
<?= $this->include('asset-movement/scripts/script') ?>
<?= $this->endSection() ?>