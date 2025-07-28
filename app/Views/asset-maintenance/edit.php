<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/partials/breadcrumb') ?>

<form class="needs-validation" id="create-maintenance-form" novalidate>
  <?= csrf_field(); ?>
  <input type="hidden" name="id" value="<?= $maintenance['id'] ?>">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="select-codes">Kode Aset <span class="text-danger">*</span></label>
              <select class="form-select select2" id="select-codes" name="item_id" disabled>
                <option value="<?= $maintenance['asset_fixed_id'] ?>" selected><?= $qrCode['content'] ?></option>
              </select>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="form-name">Nama Barang</label>
              <input class="form-control" type="text" id="form-name" name="name" placeholder="Masukkan nama barang" value="<?= $item['name'] ?>" readonly></input>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="maintenance-date">Tanggal Pemeliharaan <span class="text-danger">*</span></label>
              <input class="form-control" type="date" id="maintenance-date" name="maintenance_date" placeholder="Masukkan tanggal pemeliharaan aset" value="<?= $maintenance['maintenance_date'] ?>"></input>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="maintenance-type">Tipe Pemeliharaan <span class="text-danger">*</span></label>
              <select class="form-select" id="maintenance-type" name="maintenance_type">
                <option value="">Pilih tipe pemeliharaan</option>
                <option value="pencegahan" <?= $maintenance['maintenance_type'] == 'pencegahan' ? 'selected' : '' ?>>Pencegahan</option>
                <option value="perbaikan" <?= $maintenance['maintenance_type'] == 'perbaikan' ? 'selected' : '' ?>>Perbaikan</option>
                <option value="darurat" <?= $maintenance['maintenance_type'] == 'darurat' ? 'selected' : '' ?>>Darurat</option>
                <option value="rutin" <?= $maintenance['maintenance_type'] == 'rutin' ? 'selected' : '' ?>>Rutin</option>
              </select>
            </div>
          </div>
          <div class="col-lg-12 mb-3">
            <label class="form-label hidden" for="next-maintenance">Jadwal Pemeliharaan Berikutnya <span class="text-danger">*</span></label>
            <input class="form-control hidden" type="date" id="next-maintenance" name="next_maintenance" value="<?= $maintenance['next_maintenance'] ?? '' ?>"></input>
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="description-editor">Deskripsi Kerusakan</label>
              <textarea class="form-control" id="description-editor" name="description" rows="6"><?= htmlspecialchars($maintenance['description'] ?? '') ?></textarea>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="maintenance-editor">Tindakan Pemeliharaan</label>
              <textarea class="form-control" id="maintenance-editor" name="maintenance" rows="6"><?= htmlspecialchars($maintenance['maintenance_action'] ?? '') ?></textarea>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="maintenance-location">Lokasi Pemeliharaan</label>
              <input class="form-control" type="text" id="maintenance-location" name="maintenance_location" placeholder="Masukkan lokasi" value="<?= $maintenance['maintenance_location'] ?>"></input>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="device-status">Status Perangkat <span class="text-danger">*</span></label>
              <select class="form-select" id="device-status" name="device_status">
                <option value="">Pilih status perangkat</option>
                <option value="normal" <?= $maintenance['device_status'] == 'normal' ? 'selected' : '' ?>>Normal</option>
                <option value="rusak_ringan" <?= $maintenance['device_status'] == 'rusak_ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
                <option value="rusak_berat" <?= $maintenance['device_status'] == 'rusak_berat' ? 'selected' : '' ?>>Rusak Berat</option>
                <option value="tidak_berfungsi" <?= $maintenance['device_status'] == 'tidak_berfungsi' ? 'selected' : '' ?>>Tidak Berfungsi</option>
                <option value="dalam_perbaikan" <?= $maintenance['device_status'] == 'dalam_perbaikan' ? 'selected' : '' ?>>Dalam Perbaikan</option>
              </select>
            </div>
          </div>
          <div class="col-lg-12 mb-3">
            <label class="form-label" for="performed-by">Dilakukan Oleh</label>
            <input class="form-control" type="text" id="performed-by" name="performed_by" placeholder="Nama teknisi / petugas" value="<?= $maintenance['performed_by'] ?>"></input>
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="maintenance-cost">Biaya Pemeliharaan <span class="text-danger">*</span></label>
              <div class="input-group">
                <div class="input-group-text">Rp</div>
                <input class="form-control" type="text" id="maintenance-cost" name="maintenance_cost" placeholder="0" value="<?= number_format($maintenance['cost'], 0, ',', '.') ?>"></input>
                <input type="hidden" id="maintenance-cost-raw" value="<?= number_format($maintenance['cost'], 0, '', '') ?>" name="cost" />
              </div>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="maintenance-time">Durasi Pemeliharaan <span class="text-danger">*</span></label>
              <div class="input-group">
                <input class="form-control" type="text" id="maintenance-time" name="maintenance_time" placeholder="1 / 2 / 3" value="<?= $maintenance['duration'] ?>"></input>
                <div class="input-group-text">Hari</div>
              </div>
            </div>
          </div>
          <div class="col-lg-12 mb-3">
            <label class="form-label" for="notes-editor">Catatan</label>
            <textarea class="form-control" id="notes-editor" name="notes" rows="6"><?= htmlspecialchars($maintenance['notes'] ?? '') ?></textarea>
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

    // Function to handle maintenance type changes
    function handleMaintenanceTypeChange() {
      const maintenanceType = $('#maintenance-type').val();
      const nextMaintenanceField = $('#next-maintenance');
      const nextMaintenanceLabel = $('label[for="next-maintenance"]');

      if (maintenanceType === 'pencegahan' || maintenanceType === 'rutin') {
        nextMaintenanceField.removeClass('hidden').addClass('fade-in').attr('required', true);
        nextMaintenanceLabel.removeClass('hidden').addClass('fade-in');

        // const today = new Date().toISOString().split('T')[0];
        // nextMaintenanceField.attr('min', today);
      } else {
        nextMaintenanceField.addClass('hidden').removeClass('fade-in').removeAttr('required');
        nextMaintenanceLabel.addClass('hidden').removeClass('fade-in');
        nextMaintenanceField.val('');
      }
    }

    // Check for pre-selected value on page load
    handleMaintenanceTypeChange();

    // Handle changes when user selects different option
    $('#maintenance-type').on('change', function() {
      handleMaintenanceTypeChange();
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
      var data = {
        id: $('input[name="id"]').val(),
        maintenance_type: $('#maintenance-type').val(),
        maintenance_date: $('#maintenance-date').val(),
        next_maintenance: $('#next-maintenance').val() || null,
        description: editorRefs.desc.getData(),
        maintenance_action: editorRefs.maintenance.getData(),
        maintenance_location: $('#maintenance-location').val(),
        device_status: $('#device-status').val(),
        performed_by: $('#performed-by').val(),
        cost: $('#maintenance-cost-raw').val(),
        duration: $('#maintenance-time').val(),
        notes: editorRefs.notes.getData()
      };

      console.log(data);

      $.ajax({
        url: '/asset-maintenances/update/' + $('input[name="id"]').val(),
        method: 'PUT',
        data: JSON.stringify(data),
        dataType: 'json',
        contentType: 'application/json',
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