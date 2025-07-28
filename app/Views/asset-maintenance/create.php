<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/partials/breadcrumb') ?>

<form class="needs-validation" id="create-maintenance-form" novalidate>
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
              <label class="form-label" for="maintenance-date">Tanggal Pemeliharaan <span class="text-danger">*</span></label>
              <input class="form-control" type="date" id="maintenance-date" name="maintenance_date" placeholder="Masukkan tanggal pemeliharaan aset"></input>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="maintenance-type">Tipe Pemeliharaan <span class="text-danger">*</span></label>
              <select class="form-select" id="maintenance-type" name="maintenance_type">
                <option value="">Pilih tipe pemeliharaan</option>
                <option value="pencegahan">Pencegahan</option>
                <option value="perbaikan">Perbaikan</option>
                <option value="darurat">Darurat</option>
                <option value="rutin">Rutin</option>
              </select>
            </div>
          </div>
          <div class="col-lg-12 mb-3">
            <label class="form-label hidden" for="next-maintenance">Jadwal Pemeliharaan Berikutnya <span class="text-danger">*</span></label>
            <input class="form-control hidden" type="date" id="next-maintenance" name="next_maintenance">
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="description-editor">Deskripsi Kerusakan</label>
              <textarea class="form-control" id="description-editor" name="description" rows="6"></textarea>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="maintenance-editor">Tindakan Pemeliharaan</label>
              <textarea class="form-control" id="maintenance-editor" name="maintenance" rows="6"></textarea>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="maintenance-location">Lokasi Pemeliharaan</label>
              <input class="form-control" type="text" id="maintenance-location" name="maintenance_location" placeholder="Masukkan lokasi"></input>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="device-status">Status Perangkat <span class="text-danger">*</span></label>
              <select class="form-select" id="device-status" name="device_status">
                <option value="">Pilih status perangkat</option>
                <option value="normal">Normal</option>
                <option value="rusak_ringan">Rusak Ringan</option>
                <option value="rusak_berat">Rusak Berat</option>
                <option value="tidak_berfungsi">Tidak Berfungsi</option>
                <option value="dalam_perbaikan">Dalam Perbaikan</option>
              </select>
            </div>
          </div>
          <div class="col-lg-12 mb-3">
            <label class="form-label" for="performed-by">Dilakukan Oleh</label>
            <input class="form-control" type="text" id="performed-by" name="performed_by" placeholder="Nama teknisi / petugas">
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="maintenance-cost">Biaya Pemeliharaan <span class="text-danger">*</span></label>
              <div class="input-group">
                <div class="input-group-text">Rp</div>
                <input class="form-control" type="text" id="maintenance-cost" name="maintenance_cost" placeholder="0"></input>
                <input type="hidden" id="maintenance-cost-raw" name="cost" />
              </div>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="maintenance-time">Durasi Pemeliharaan <span class="text-danger">*</span></label>
              <div class="input-group">
                <input class="form-control" type="text" id="maintenance-time" name="maintenance_time" placeholder="1 / 2 / 3"></input>
                <div class="input-group-text">Hari</div>
              </div>
            </div>
          </div>
          <div class="col-lg-12 mb-3">
            <label class="form-label" for="notes-editor">Catatan</label>
            <textarea class="form-control" id="notes-editor" name="notes" rows="6"></textarea>
          </div>
          <div class="mt-3 d-flex gap-2 justify-content-end">
            <a href="/asset-maintenances" class="btn btn-secondary w-sm waves-effect waves-light">Kembali</a>
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
                items_name: asset.items_name
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

    $('#select-codes').on('select2:select', function(e) {
      const selectedData = e.params.data;
      if (selectedData && selectedData.items_name) {
        $('#form-name').val(selectedData.items_name);
      }
    });

    $('#select-codes').on('select2:clear', function() {
      $('#form-name').val('');
    });

    // Show/hide next maintenance date based on maintenance type
    $('#maintenance-type').on('change', function() {
      const maintenanceType = $(this).val();
      const nextMaintenanceField = $('#next-maintenance');
      const nextMaintenanceLabel = $('label[for="next-maintenance"]');

      if (maintenanceType === 'pencegahan' || maintenanceType === 'rutin') {
        nextMaintenanceField.removeClass('hidden').attr('required', true);
        nextMaintenanceLabel.removeClass('hidden');
      } else {
        nextMaintenanceField.addClass('hidden').removeAttr('required');
        nextMaintenanceLabel.addClass('hidden');
        nextMaintenanceField.val('');
      }
    });

    // Format currency input
    $('#maintenance-cost').on('input', function() {
      let raw = $(this).val().replace(/[^\d]/g, '');
      let formatted = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

      $(this).val(formatted);
      $('#maintenance-cost-raw').val(raw);
    });

    // Validate duration input (only numbers)
    $('#maintenance-time').on('input', function() {
      $(this).val($(this).val().replace(/[^\d]/g, ''));
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

    initEditor('#description-editor', 'desc');
    initEditor('#maintenance-editor', 'maintenance');
    initEditor('#notes-editor', 'notes');

    $('#create-maintenance-form').on('submit', function(e) {
      e.preventDefault();
      submitData();
    });

    function submitData() {
      var formData = new FormData();

      formData.append('asset_fixed_id', $('#select-codes').val());
      formData.append('maintenance_type', $('#maintenance-type').val());
      formData.append('maintenance_date', $('#maintenance-date').val());
      formData.append('description', editorRefs.desc.getData());
      formData.append('maintenance_action', editorRefs.maintenance.getData());
      formData.append('maintenance_location', $('#maintenance-location').val());
      formData.append('device_status', $('#device-status').val());
      formData.append('performed_by', $('#performed-by').val());
      formData.append('cost', $('#maintenance-cost-raw').val());
      formData.append('duration', $('#maintenance-time').val());
      formData.append('notes', editorRefs.notes.getData());

      if ($('#next-maintenance').val() != '' && $('#next-maintenance').val() != null) {
        formData.append('next_maintenance', $('#next-maintenance').val());
      }

      for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
      }

      $.ajax({
        url: '/asset-maintenances/store',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.status == 'success') {
            showToast(response.toast.type, response.toast.message);
            setTimeout(function() {
              window.location.href = '/asset-maintenances';
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