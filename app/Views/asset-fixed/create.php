<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/partials/breadcrumb') ?>

<form class="needs-validation" id="create-asset-form" novalidate>
  <?= csrf_field(); ?>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="select-managers">Pengelola Aset <span class="text-danger">*</span></label>
              <select class="form-select select2" id="select-managers" name="asset_managers_id">
              </select>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="select-item">Nama Aset / Barang <span class="text-danger">*</span></label>
              <select class="form-select select2" id="select-item" name="asset_item_id" disabled>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="form-quantity">Jumlah <span class="text-danger">*</span></label>
              <input class="form-control" type="number" id="form-quantity" name="quantity" placeholder="Masukkan jumlah asset"></input>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="select-unit">Satuan <span class="text-danger">*</span></label>
              <select class="form-select" id="select-unit" name="unit">
                <option value="box">Box</option>
                <option value="pcs">Pcs</option>
                <option value="roll">Roll</option>
                <option value="set">Set</option>
                <option value="unit">Unit</option>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="select-condition">Kondisi <span class="text-danger">*</span></label>
              <select class="form-select" id="select-condition" name="condition">
                <option value="baru">Baru</option>
                <option value="bekas">Bekas</option>
                <option value="lama">Lama</option>
                <option value="rusak">Rusak</option>
                <option value="hilang">Hilang</option>
              </select>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="select-locations">Lokasi Aset <span class="text-danger">*</span></label>
              <select class="form-control select2" id="select-locations" name="asset_location_id">
                <option value="">Pilih Lokasi Aset</option>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="form-responsible">Penanggung jawab Aset</label>
              <input class="form-control" type="text" id="form-responsible" name="responsible_person" placeholder="Masukkan nama penanggung jawab aset"></input>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="form-economic-life">Umur Ekonomis <span class="text-danger">*</span></label>
              <div class="input-group">
                <input class="form-control" type="text" id="form-economic-life" name="economic_life" placeholder="1 / 2 / 3 / 4"></input>
                <div class="input-group-text"><b>Tahun</b></div>
              </div>
            </div>
          </div>
          <div class="mb-3 wrap">
            <label class="form-label" for="form-cost">Nilai Aset <span class="text-danger">*</span></label>
            <div class="input-group">
              <div class="input-group-text">Rp</div>
              <input class="form-control" type="text" id="form-cost" name="acquisition_cost" placeholder="000"></input>
            </div>
          </div>
          <div class="mb-3 wrap">
            <label class="form-label" for="formCheck2">Generate QR Code?</label>
            <!-- <div class="form-check">
              <input class="form-check-input" type="checkbox" id="formCheck2">
              <label class="form-check-label" for="formCheck2">
                Ya, generate qr code
              </label>
            </div> -->
            <div class="square-switch">
              <input type="checkbox" id="square-switch1" switch="none">
              <label for="square-switch1" data-on-label="Yes" data-off-label="No"></label>
            </div>
          </div>

          <div class="mt-3 d-flex gap-2 justify-content-end">
            <a href="/asset-fixed" class="btn btn-secondary w-sm waves-effect waves-light">Kembali</a>
            <button class="btn btn-primary w-sm waves-effect waves-light" type="submit">Simpan</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<?= $this->endSection() ?>

<?= $this->section('custom-js') ?>
<script>
  const itemData = <?= json_encode($items) ?>;
  $(document).ready(function() {
    $('#select-locations').select2({
      placeholder: 'Pilih Lokasi Aset',
      width: '100%',
      language: {
        noResults: function() {
          return "Tidak ada hasil ditemukan";
        },
        searching: function() {
          return "Mencari...";
        }
      },
      ajax: {
        url: '<?= base_url('select/get-asset-locations') ?>',
        dataType: 'json',
        delay: 250,
        processResults: function(data) {
          return {
            results: data.map(function(location) {
              return {
                id: location.id,
                text: location.name
              };
            })
          };
        }
      }
    });

    $('#select-managers').select2({
      placeholder: 'Pilih Pengelola',
      width: '100%',
      language: {
        noResults: function() {
          return "Tidak ada hasil ditemukan";
        },
        searching: function() {
          return "Mencari...";
        }
      },
      ajax: {
        url: '<?= base_url('select/get-asset-managers') ?>',
        dataType: 'json',
        delay: 250,
        processResults: function(data) {
          return {
            results: data.map(function(manager) {
              return {
                id: manager.id,
                text: manager.name
              };
            })
          };
        }
      }
    });

    $('#select-item').select2({
      placeholder: 'Pilih Barang',
      width: '100%',
      language: {
        noResults: function() {
          return "Tidak ada hasil ditemukan";
        }
      }
    });

    $('#select-managers').on('change', function() {
      const selectedManagersId = $(this).val();
      const filtered = itemData.filter(p => p.asset_managers_id == selectedManagersId);

      const $itemSelect = $('#select-item');

      // Solusi yang lebih aman - cek dengan try-catch
      try {
        if ($itemSelect.data('select2')) {
          $itemSelect.select2('destroy');
        }
      } catch (e) {
        // Abaikan error jika select2 belum diinisialisasi
      }

      $itemSelect.prop('disabled', false).empty().append(
        '<option selected disabled hidden>Pilih Barang</option>'
      );

      filtered.forEach(items => {
        $itemSelect.append(
          `<option value="${items.id}">${items.name}</option>`
        );
      });

      $itemSelect.select2({
        placeholder: 'Pilih Barang',
        width: '100%',
        language: {
          noResults: function() {
            return "Tidak ada hasil ditemukan";
          },
          searching: function() {
            return "Mencari...";
          }
        }
      });
    });

    $('#create-asset-form').on('submit', function(e) {
      e.preventDefault();
      submitData();
    });

    function submitData() {
      var formData = new FormData();

      formData.append('asset_manager_id', $('#select-managers').val());
      formData.append('item_id', $('#select-item').val());
      formData.append('quantity', $('#form-quantity').val());
      formData.append('unit', $('#select-unit').val());
      formData.append('condition', $('#select-condition').val());
      formData.append('asset_location_id', $('#select-locations').val());
      formData.append('responsible_person', $('#form-responsible').val());
      formData.append('economic_life', $('#form-economic-life').val());
      formData.append('acquisition_cost', $('#form-cost').val());
      formData.append('generate_qr', $('#square-switch1').is(':checked') ? 1 : 0);

      $.ajax({
        url: '/asset-fixed/store',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.status == 'success') {
            showToast(response.toast.type, response.toast.message);
            setTimeout(function() {
              window.location.href = '/asset-fixed';
            }, 2000);
          }
        },
        error: function(xhr, status, error) {
          if (xhr.status == 400) {
            console.log(error);
            showToast('danger', error);
          } else if (xhr.status == 500) {
            console.log(error + 'Internal server error.');
            showToast('danger', error);
          } else {
            try {
              var res = JSON.parse(xhr.responseText);
              if (res.message) {
                console.log('Error: ' + res.message);
                showToast('danger', res.message);
              }
              if (res.error) {
                $('.form-control').removeClass('is-invalid').removeClass('is-valid');
                $('.invalid-feedback').remove();

                $.each(res.error, function(field, message) {
                  let input = $('[name="' + field + '"]');
                  input.addClass('is-invalid');
                  input.after('<div class="invalid-feedback">' + message + '</div>');
                });
              }
            } catch (e) {
              console.log('Raw error: ' + xhr.responseText);
              showToast('danger', 'Terjadi kesalahan yang tidak diketahui.');
            }
          }
        },
        complete: function() {}
      });
    }
  });
</script>
<?= $this->endSection() ?>