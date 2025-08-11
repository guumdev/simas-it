<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/partials/breadcrumb') ?>

<form class="needs-validation" id="create-movement-form" novalidate>
  <?= csrf_field(); ?>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="select-codes">Kode Aset <span class="text-danger">*</span></label>
              <select class="form-select select2" id="select-codes" name="item_id">
              </select>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="form-name">Nama Barang</label>
              <input class="form-control" type="text" id="form-name" name="name" placeholder="Masukkan nama barang" readonly></input>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="from-location">Lokasi Asal</label>
              <input class="form-control" type="text" id="from-location" name="from_location" placeholder="Masukkan lokasi asal" readonly></input>
              <input type="hidden" id="from-location-id" name="from_location_id">
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="to-location">Lokasi Tujuan <span class="text-danger">*</span></label>
              <select class="form-select select2" id="to-location" name="to_location">
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="movement-date">Tanggal Perpindahan <span class="text-danger">*</span></label>
              <input class="form-control" type="date" id="movement-date" name="movement_date" placeholder="Masukkan tanggal perpindahan aset"></input>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="movement-type">Tipe Perpindahan <span class="text-danger">*</span></label>
              <select class="form-select" id="movement-type" name="movement_type">
                <option value="">Pilih tipe perpindahan</option>
                <option value="transfer">Transfer</option>
                <option value="maintenance">Maintenance</option>
                <option value="return">Pengembalian</option>
              </select>
            </div>
          </div>
          <div class="col-lg-12 mb-3">
            <label class="form-label" for="moved-by">Dilakukan Oleh</label>
            <input class="form-control" type="text" id="moved-by" name="moved_by" placeholder="Nama teknisi / petugas">
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="condition-before">Kondisi Sebelum Perpindahan <span class="text-danger">*</span></label>
              <select class="form-select" id="condition-before" name="condition_before">
                <option value="">Pilih kondisi</option>
                <option value="normal">Normal</option>
                <option value="rusak_ringan">Rusak ringan</option>
                <option value="rusak_berat">Rusak berat</option>
                <option value="tidak_berfungsi">Tidak berfungsi</option>
              </select>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="condition-after">Kondisi Sesudah Perpindahan <span class="text-danger">*</span></label>
              <select class="form-select" id="condition-after" name="condition_after">
                <option value="">Pilih kondisi</option>
                <option value="normal">Normal</option>
                <option value="rusak_ringan">Rusak ringan</option>
                <option value="rusak_berat">Rusak berat</option>
                <option value="tidak_berfungsi">Tidak berfungsi</option>
              </select>
            </div>
          </div>
          <div class="col-lg-12 mb-3">
            <label class="form-label" for="notes-editor">Catatan</label>
            <textarea class="form-control" id="notes-editor" name="notes" rows="6"></textarea>
          </div>
          <div class="mt-3 d-flex gap-2 justify-content-end">
            <a href="/asset-movements" class="btn btn-secondary w-sm waves-effect waves-light">Kembali</a>
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
  $(document).ready(function() {
    $('#select-codes').select2({
      placeholder: 'Pilih Aset',
      width: '100%',
      language: {
        noResults: function() {
          return "Tidak ada hasil ditemukan";
        },
        searching: function() {
          return "Mencari...";
        },
        inputTooShort: function() {
          return "Ketik minimal 2 karakter untuk mencari";
        }
      },
      ajax: {
        url: '<?= base_url('select/get-asset-code') ?>',
        dataType: 'json',
        delay: 250,
        data: function(params) {
          return {
            q: params.term,
            page: params.page || 1
          };
        },
        processResults: function(data, params) {
          params.page = params.page || 1;

          return {
            results: data.results.map(function(asset) {
              return {
                id: asset.id,
                text: asset.qr_codes,
                items_name: asset.items_name,
                items_location: asset.items_location,
                items_location_id: asset.items_location_id
              };
            }),
            pagination: {
              more: data.pagination.more
            }
          };
        },
        cache: true
      }
    });

    $('#to-location').select2({
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

    $('#select-codes').on('select2:select', function(e) {
      const selectedData = e.params.data;
      if (selectedData && selectedData.items_name && selectedData.items_location && selectedData.items_location_id) {
        $('#form-name').val(selectedData.items_name);
        $('#from-location').val(selectedData.items_location);
        $('#from-location-id').val(selectedData.items_location_id);
      }
    });

    $('#select-codes').on('select2:clear', function() {
      $('#form-name').val('');
      $('#from-location').val('');
      $('#from-location-id').val('');
    });

    let editorRefs = {};

    function initEditor(selector, key) {
      ClassicEditor
        .create(document.querySelector(selector))
        .then(editor => {
          editorRefs[key] = editor;
        })
        .catch(error => {
          console.error(error);
        })
    }
    initEditor('#notes-editor', 'notes');

    $('#create-movement-form').on('submit', function(e) {
      e.preventDefault();
      submitData();
    });

    function submitData() {
      var data = {
        asset_fixed_id: $('#select-codes').val(),
        from_location_id: $('#from-location-id').val(),
        to_location_id: $('#to-location').val(),
        movement_type: $('#movement-type').val(),
        movement_date: $('#movement-date').val(),
        moved_by: $('#moved-by').val(),
        condition_before: $('#condition-before').val(),
        condition_after: $('#condition-after').val(),
        notes: editorRefs.notes.getData(),
      };

      console.log(data);

      $.ajax({
        url: '/asset-movements/store',
        method: 'POST',
        data: data,
        success: function(response) {
          if (response.status == 'success') {
            showToast(response.toast.type, response.toast.message);
            setTimeout(function() {
              window.location.href = '/asset-movements';
            }, 2000);
          }
        },
        erorr: function(xhr, status, error) {
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