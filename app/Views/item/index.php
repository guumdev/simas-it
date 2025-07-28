<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/partials/breadcrumb') ?>

<div class="row">
  <div class="col-12">
    <?= $this->include('item/partials/counter') ?>

    <!-- Alert Messages -->
    <?= $this->include('layouts/partials/alert') ?>

    <div class="card">
      <div class="card-header bg-primary-subtle">
        <div class="row align-items-end g-3">
          <div class="col-lg-6">
            <div class="d-flex gap-2">
              <a href="<?= base_url('/items/create'); ?>" class="btn btn-primary w-sm waves-effect waves-light btn-create">
                <i class="uil-plus me-2"></i>Buat
              </a>
              <button type="button" class="btn btn-success w-sm waves-effect waves-light" onclick="showUploadContent()">
                <i class="uil-file-times me-2"></i>Import
              </button>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="d-flex justify-content-end gap-2">
              <a href="<?= base_url('/items/excel/export') ?>" class="btn btn-success w-sm waves-effect waves-light">
                <i class="uil-export me-2"></i>Export
              </a>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body" id="cardBodyContent">

        <!-- Default Content -->
        <div id="tableContent">
          <div class="row align-items-end g-3 mb-3">
            <div class="col-lg-3">
              <label for="managers-filter" class="form-label">Pengelola Aset</label>
              <select class="form-control select2" id="managers-filter" name="managers_filter">
                <option value="">Pilih Pengelola</option>
              </select>
            </div>
            <div class="col-lg-3">
              <label for="year-filter" class="form-label">Tahun Perolehan</label>
              <input type="text" class="form-control" id="year-filter" placeholder="Pilih tahun">
            </div>
            <div class="col-lg-6">
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-danger w-sm waves-effect waves-light" id="btn-reset">
                  <i class="uil-redo me-2"></i>Reset
                </button>
              </div>
            </div>
          </div>
          <table id="item-dtable" class="table table-striped table-bordered" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead>
              <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 10%;">Pengelola</th>
                <th>Kategori</th>
                <th>Nama</th>
                <th>Nomor Seri</th>
                <th>Perolehan</th>
                <th>Foto Barang</th>
                <th style="width: 15%;">Aksi</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div> <!-- End Default Content -->

        <!-- Upload Content (Hidden by default) -->
        <div id="uploadContent" style="display: none;">
          <div class="row">
            <div class="col-md-8">
              <form action="<?= base_url('/items/excel/import') ?>" method="post" enctype="multipart/form-data" id="importForm">
                <?= csrf_field() ?>

                <div class="mb-4">
                  <label for="excel_file" class="form-label fw-bold">Pilih File Excel</label>
                  <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
                  <div class="form-text">
                    <i class="uil-info-circle me-1"></i>
                    Format yang didukung: .xlsx, .xls (maksimal 2MB)
                  </div>
                </div>

                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-primary waves-effect waves-light" id="importBtn">
                    <i class="uil-file-times me-2"></i>
                    Import Data
                  </button>
                  <button class="btn btn-primary waves-effect waves-light" type="button" disabled id="importSpinner" style="display: none;">
                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                    Mengimport...
                  </button>
                  <button type="button" class="btn btn-light waves-effect" onclick="showTableContent()">
                    <i class="uil-times me-2"></i>
                    Batal
                  </button>
                </div>
              </form>
            </div>
            <div class="col-md-4">
              <div class="card border-0 bg-light-subtle">
                <div class="card-body">
                  <h6 class="card-title text-primary">
                    <i class="uil-info-circle me-2"></i>
                    Format Excel
                  </h6>
                  <p class="card-text small mb-3">
                    Kolom yang diperlukan dalam file Excel:
                  </p>
                  <div class="table-responsive">
                    <table class="table table-sm table-borderless">
                      <tbody>
                        <tr>
                          <td class="fw-bold">A:</td>
                          <td>Pengelola Aset</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">B:</td>
                          <td>Kategori Aset</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">C:</td>
                          <td>Nama Barang</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">D:</td>
                          <td>Merek</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">E:</td>
                          <td>Model</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">F:</td>
                          <td>Nomor Seri</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">G:</td>
                          <td>Vendor</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">H:</td>
                          <td>Spesifikasi</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">I:</td>
                          <td>Tanggal Perolehan</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="alert alert-info p-2 small">
                    <i class="uil-lightbulb-alt me-1"></i>
                    Download template untuk memastikan format yang benar
                  </div>
                  <a href="<?= base_url('/items/excel/template') ?>" class="btn btn-info w-sm waves-effect waves-light">
                    <i class="uil-download-alt me-2"></i>Download Template Excel
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div> <!-- End Upload Content -->
      </div>
    </div>
  </div>
</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('custom-js') ?>
<?= $this->include('item/scripts/datatables') ?>
<?= $this->include('item/scripts/script') ?>

<?= $this->endSection() ?>