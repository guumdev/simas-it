<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/partials/breadcrumb') ?>

<form class="needs-validation" id="edit-movement-form" novalidate>
  <?= csrf_field(); ?>
  <input type="hidden" name="id" value="<?= $datas['id'] ?>">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="select-codes">Kode Aset <span class="text-danger">*</span></label>
              <select class="form-select select2" id="select-codes" name="item_id" disabled>
                <option value="<?= $datas['asset_fixed_id'] ?>" selected><?= $datas['item_code'] ?></option>
              </select>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="form-name">Nama Barang</label>
              <input class="form-control" type="text" id="form-name" name="name" placeholder="Masukkan nama barang" value="<?= $datas['item_name'] ?>" readonly></input>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="from-location">Lokasi Asal</label>
              <input class="form-control" type="text" id="from-location" name="from_location" placeholder="Masukkan lokasi asal" value="<?= $datas['from_location_name'] ?>" readonly></input>
              <input type="hidden" id="from-location-id" name="from_location_id" value="<?= $datas['from_location_id'] ?>">
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="to-location">Lokasi Tujuan <span class="text-danger">*</span></label>
              <select class="form-select select2" id="to-location" name="to_location">
                <option value="<?= $datas['to_location_id'] ?>" selected><?= $datas['to_location_name'] ?></option>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="movement-date">Tanggal Perpindahan <span class="text-danger">*</span></label>
              <input class="form-control" type="date" id="movement-date" name="movement_date" placeholder="Masukkan tanggal perpindahan aset" value="<?= $datas['movement_date'] ?>"></input>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="movement-type">Tipe Perpindahan <span class="text-danger">*</span></label>
              <select class="form-select" id="movement-type" name="movement_type">
                <option value="">Pilih tipe perpindahan</option>
                <option value="transfer" <?= $datas['movement_type'] == 'transfer' ? 'selected' : '' ?>>Transfer</option>
                <option value="maintenance" <?= $datas['movement_type'] == 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                <option value="return" <?= $datas['movement_type'] == 'return' ? 'selected' : '' ?>>Pengembalian</option>
              </select>
            </div>
          </div>
          <div class="col-lg-12 mb-3">
            <label class="form-label" for="moved-by">Dilakukan Oleh</label>
            <input class="form-control" type="text" id="moved-by" name="moved_by" placeholder="Nama teknisi / petugas" value="<?= $datas['moved_by'] ?>"></input>
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="condition-before">Kondisi Sebelum Perpindahan <span class="text-danger">*</span></label>
              <select class="form-select" id="condition-before" name="condition_before">
                <option value="">Pilih kondisi</option>
                <option value="normal" <?= $datas['condition_before'] == 'normal' ? 'selected' : '' ?>>Normal</option>
                <option value="rusak_ringan" <?= $datas['condition_before'] == 'rusak_ringan' ? 'selected' : '' ?>>Rusak ringan</option>
                <option value="rusak_berat" <?= $datas['condition_before'] == 'rusak_berat' ? 'selected' : '' ?>>Rusak berat</option>
                <option value="tidak_berfungsi" <?= $datas['condition_before'] == 'tidak_berfungsi' ? 'selected' : '' ?>>Tidak berfungsi</option>
              </select>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="condition-after">Kondisi Sesudah Perpindahan <span class="text-danger">*</span></label>
              <select class="form-select" id="condition-after" name="condition_after">
                <option value="normal" <?= $datas['condition_after'] == 'normal' ? 'selected' : '' ?>>Normal</option>
                <option value="rusak_ringan" <?= $datas['condition_after'] == 'rusak_ringan' ? 'selected' : '' ?>>Rusak ringan</option>
                <option value="rusak_berat" <?= $datas['condition_after'] == 'rusak_berat' ? 'selected' : '' ?>>Rusak berat</option>
                <option value="tidak_berfungsi" <?= $datas['condition_after'] == 'tidak_berfungsi' ? 'selected' : '' ?>>Tidak berfungsi</option>
              </select>
            </div>
          </div>
          <div class="col-lg-12 mb-3">
            <label class="form-label" for="notes-editor">Catatan</label>
            <textarea class="form-control" id="notes-editor" name="notes" rows="6"><?= htmlspecialchars($datas['notes'] ?? '') ?></textarea>
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

    $('#edit-movement-form').on('submit', function(e) {
      e.preventDefault();
      submitData();
    });

    function submitData() {
      var data = {
        id: $('input[name="id"]').val(),
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
        url: '/asset-movements/update/' + $('input[name="id"]').val(),
        method: 'PUT',
        data: JSON.stringify(data),
        dataType: 'json',
        contentType: 'application/json',
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