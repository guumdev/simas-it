<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/partials/breadcrumb') ?>

<div class="row">
  <div class="col-12">

    <!-- Alert Messages -->
    <?= $this->include('layouts/partials/alert') ?>

    <!-- QR Code Modal -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="qrModalLabel">
              <i class="fas fa-qrcode me-2"></i>QR Code
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center">
            <div class="p-3">
              <img id="modalQrImage" src="" alt="QR Code" class="modal-qr-image shadow" style="max-width: 100%; height: auto; border-radius: 8px;">
              <p class="mt-3"><strong>Kode Aset:</strong> <span id="modalAssetCode"></span></p>
              <p class="mt-3">Scan QR Code ini untuk melihat detail asset.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header bg-primary-subtle">
        <div class="row align-items-end g-3">
          <div class="col-lg-6">
            <div class="d-flex gap-2">
              <a href="<?= base_url('/asset-fixed/create'); ?>" class="btn btn-primary w-sm waves-effect waves-light btn-create">
                <i class="uil-plus me-2"></i>Buat
              </a>
              <button type="button" class="btn btn-success w-sm waves-effect waves-light" onclick="showUploadContent()">
                <i class="uil-file-times me-2"></i>Import
              </button>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="d-flex justify-content-end gap-2">
              <button id="generate-selected" class="btn btn-info w-sm waves-effect waves-light">
                <i class="mdi mdi-qrcode me-2"></i>Generate QR Code
              </button>
              <button id="print-selected" class="btn btn-info w-sm waves-effect waves-light">
                <i class="mdi mdi-printer me-2"></i>Cetak QR Code
              </button>
              <a href="<?= base_url('/asset-fixed/excel/export') ?>" class="btn btn-success w-sm waves-effect waves-light">
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
              <label for="locations-filter" class="form-label">Lokasi Aset</label>
              <select class="form-control select2" id="locations-filter" name="locations_filter">
                <option value="">Pilih Lokasi Aset</option>
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
          <table id="asset-fixed-dtable" class="table table-striped table-bordered" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead>
              <tr>
                <th style="width: 5%;"><input type="checkbox" id="select-all"></th>
                <th style="width: 5%;">ID</th>
                <th>Kode Aset</th>
                <th>Nama</th>
                <th>Lokasi</th>
                <th>Kondisi</th>
                <th>Perolehan</th>
                <th>QR Code</th>
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
              <form action="<?= base_url('/asset-fixed/excel/import') ?>" method="post" enctype="multipart/form-data" id="importForm">
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
                          <td>ID Pengelola Aset</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">B:</td>
                          <td>ID Barang</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">C:</td>
                          <td>Jumlah</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">D:</td>
                          <td>Satuan</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">E:</td>
                          <td>Kondisi</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">F:</td>
                          <td>ID Lokasi Aset</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">G:</td>
                          <td>Penanggung Jawab Aset</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">H:</td>
                          <td>Umur Ekonomis Aset</td>
                        </tr>
                        <tr>
                          <td class="fw-bold">I:</td>
                          <td>Nilai Aset</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="alert alert-info p-2 small">
                    <i class="uil-lightbulb-alt me-1"></i>
                    Download template untuk memastikan format yang benar
                  </div>
                  <a href="<?= base_url('/asset-fixed/excel/template') ?>" class="btn btn-info w-sm waves-effect waves-light">
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
<?= $this->include('asset-fixed/scripts/script') ?>
<?= $this->include('asset-fixed/scripts/datatable') ?>

<script>
  // select/checkbox handle
  $('#asset-fixed-dtable thead').on('click', '#select-all', function() {
    var isChecked = $(this).is(':checked');
    $('.table-select').prop('checked', isChecked);
  });

  // button cetak qr handle
  $('#print-selected').on('click', function() {
    const selectedData = [];

    $('.table-select:checked').each(function() {
      const id = $(this).val();
      const qr = $(this).attr('qr-image');

      // Hanya masukkan data yang valid
      if (id && qr && qr !== 'null' && qr !== 'undefined') {
        selectedData.push({
          id,
          qr
        });
      }
    });

    if (selectedData.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'Peringatan',
        text: 'Tidak ada aset dengan QR code yang valid untuk dicetak!',
      });
      return;
    }

    const query = selectedData.map(item => `ids[]=${item.id}`).join('&');
    console.log('Valid data count:', selectedData.length);

    window.open(`<?= base_url('aset-berwujud/print') ?>?${query}`, '_blank');
  });

  $('#generate-selected').on('click', function() {
    const selectedIds = [];

    // validasi checkbox yang dipilih
    $('.table-select:checked').each(function() {
      const qrId = $(this).attr('qr');
      // validasi qr ID tidak kosong/null/undefined
      if (qrId && qrId !== 'null' && qrId !== 'undefined' && qrId.trim() !== '') {
        selectedIds.push(qrId.trim());
      }
    });

    if (selectedIds.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'Peringatan',
        text: 'Pilih minimal satu aset untuk digenerate QR-nya!',
      });
      return;
    }

    // show loading state
    const button = $(this);
    const originalText = button.text();
    button.prop('disabled', true).text('Generating...');

    // show konfirmasi sebelum generate
    Swal.fire({
      title: 'Konfirmasi',
      text: `Generate QR code untuk ${selectedIds.length} aset?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, Generate',
      cancelButtonText: 'Batal',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        // proses generate
        performQRGeneration(selectedIds, button, originalText);
      } else {
        // reset button state jika dibatalkan
        button.prop('disabled', false).text(originalText);
        $('.table-select:checked').prop('checked', false);
      }
    });
  });

  function performQRGeneration(selectedIds, button, originalText) {
    $.ajax({
      url: `/asset-fixed/generate/qr`,
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({
        id: selectedIds
      }),
      // add timeout
      timeout: 30000, // 30 detik
      success: function(res) {
        // reset button state
        button.prop('disabled', false).text(originalText);

        // handle semua status response
        if (res.status === 'success') {
          // semua berhasil
          Swal.fire({
            icon: 'success',
            title: res.toast.title,
            text: res.toast.message,
            timer: 3000,
            showConfirmButton: false
          });
          $('#asset-fixed-dtable').DataTable().ajax.reload(function() {});

        } else if (res.status === 'partial') {
          // sebagian berhasil
          Swal.fire({
            icon: 'warning',
            title: res.toast.title,
            html: formatPartialMessage(res.toast.message, res.toast.summary),
            confirmButtonText: 'OK'
          });
          $('#asset-fixed-dtable').DataTable().ajax.reload(function() {});

        } else if (res.status === 'info') {
          // semua sudah ter-generate
          Swal.fire({
            icon: 'info',
            title: res.toast.title,
            text: res.toast.message,
            confirmButtonText: 'OK'
          });
          // cleanup checkbox
          $('.table-select:checked').prop('checked', false);

        } else if (res.status === 'error') {
          Swal.fire({
            icon: 'error',
            title: res.toast.title || 'Gagal',
            text: res.toast.message || 'Terjadi kesalahan saat generate QR code'
          });

        } else {
          // status anomali
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Response tidak dikenal dari server'
          });
        }
      },
      error: function(xhr, status, error) {
        // reset button state
        button.prop('disabled', false).text(originalText);

        // error handling
        let errorMessage = 'Terjadi kesalahan tidak terduga';
        let errorTitle = 'Gagal Generate QR';

        if (status === 'timeout') {
          errorMessage = 'Request timeout. Silakan coba lagi dalam beberapa saat.';
          errorTitle = 'Timeout';
        } else if (xhr.status === 400) {
          errorMessage = 'Data yang dikirim tidak valid';
          // get detail pesan dari response
          try {
            const errorRes = JSON.parse(xhr.responseText);
            if (errorRes.message) {
              errorMessage = errorRes.message;
            }
          } catch (e) {
            // pake pesan default kalau parsing gagal
            errorMessage = 'Data yang dikirim tidak valid';
            errorTitle = 'Gagal Generate QR';
          }
        } else if (xhr.status === 404) {
          errorMessage = 'Endpoint tidak ditemukan';
        } else if (xhr.status === 500) {
          errorMessage = 'Kesalahan server internal';
          try {
            const errorRes = JSON.parse(xhr.responseText);
            if (errorRes.message) {
              errorMessage = errorRes.message;
            }
          } catch (e) {
            errorMessage = 'Kesalahan server internal';
            errorTitle = 'Gagal Generate QR';
          }
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMessage = xhr.responseJSON.message;
        } else if (xhr.responseText) {
          try {
            const errorRes = JSON.parse(xhr.responseText);
            if (errorRes.message) {
              errorMessage = errorRes.message;
            }
          } catch (e) {
            errorMessage = 'Terjadi kesalahan tidak terduga';
            errorTitle = 'Gagal Generate QR';
          }
        }

        // Log error untuk debugging
        console.error('Generate QR Error:', {
          status: xhr.status,
          statusText: xhr.statusText,
          responseText: xhr.responseText,
          error: error
        });

        Swal.fire({
          icon: 'error',
          title: errorTitle,
          text: errorMessage,
          footer: xhr.status ? `Error Code: ${xhr.status}` : null
        });
      }
    });
  }

  function formatPartialMessage(message, summary) {
    if (!summary) return message;

    return `
        <div class="text-left">
          <p>${message}</p>
            <p><strong>Ringkasan:</strong></p>
            <ul class="list-unstyled">
                <li>🟩 Berhasil: ${summary.success}</li>
                <li>🟦 Sudah ada: ${summary.already_generated}</li>
                <li>🟥 Gagal: ${summary.failed}</li>
                <li>🟨 Total: ${summary.total}</strong></li>
            </ul>
            <hr>
        </div>
    `;
  }
</script>

<?= $this->endSection() ?>