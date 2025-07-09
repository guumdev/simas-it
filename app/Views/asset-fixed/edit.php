<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/partials/breadcrumb') ?>

<form class="needs-validation" id="edit-asset-form" novalidate>
  <?= csrf_field(); ?>
  <input type="hidden" name="id" value="<?= $assetFixedData['id'] ?>">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="select-managers">Pengelola Aset</label>
              <select class="form-select select2" id="select-managers" name="asset_managers_id" disabled>
                <option value="<?= $assetManagerData['id'] ?>" selected><?= $assetManagerData['code'] ?> - <?= $assetManagerData['name'] ?></option>
              </select>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="select-item">Nama Aset / Barang</label>
              <select class="form-select select2" id="select-item" name="asset_item_id" disabled>
                <option value="<?= $itemData['id'] ?>" selected><?= $itemData['name'] ?></option>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-lg">
              <label class="form-label" for="select-unit">Satuan <span class="text-danger">*</span></label>
              <select class="form-select" id="select-unit" name="unit" required>
                <option value="box" <?= $assetFixedData['unit'] == 'box' ? 'selected' : '' ?>>Box</option>
                <option value="pcs" <?= $assetFixedData['unit'] == 'pcs' ? 'selected' : '' ?>>Pcs</option>
                <option value="roll" <?= $assetFixedData['unit'] == 'roll' ? 'selected' : '' ?>>Roll</option>
                <option value="set" <?= $assetFixedData['unit'] == 'set' ? 'selected' : '' ?>>Set</option>
                <option value="unit" <?= $assetFixedData['unit'] == 'unit' ? 'selected' : '' ?>>Unit</option>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="select-condition">Kondisi <span class="text-danger">*</span></label>
              <select class="form-select" id="select-condition" name="condition">
                <option value="baru" <?= $assetFixedData['condition'] == 'baru' ? 'selected' : '' ?>>Baru</option>
                <option value="bekas" <?= $assetFixedData['condition'] == 'bekas' ? 'selected' : '' ?>>Bekas</option>
                <option value="lama" <?= $assetFixedData['condition'] == 'lama' ? 'selected' : '' ?>>Lama</option>
                <option value="rusak" <?= $assetFixedData['condition'] == 'rusak' ? 'selected' : '' ?>>Rusak</option>
                <option value="hilang" <?= $assetFixedData['condition'] == 'hilang' ? 'selected' : '' ?>>Hilang</option>
              </select>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="select-locations">Lokasi Aset</label>
              <select class="form-control select2" id="select-locations" name="asset_location_id" disabled>
                <option value="<?= $assetLocationData['id'] ?>" selected><?= $assetLocationData['code'] ?> - <?= $assetLocationData['name'] ?></option>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="form-responsible">Penanggung jawab Aset <span class="text-danger">*</span></label>
              <input class="form-control" type="text" id="form-responsible" name="responsible_person" placeholder="Masukkan nama penanggung jawab aset" value="<?= $assetFixedData['responsible_person'] ?>"></input>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="form-economic-life">Umur Ekonomis <span class="text-danger">*</span></label>
              <div class="input-group">
                <input class="form-control" type="text" id="form-economic-life" name="economic_life" placeholder="1 / 2 / 3 / 4" value="<?= $assetFixedData['economic_life'] ?>"></input>
                <div class="input-group-text"><b>Tahun</b></div>
              </div>
            </div>
          </div>
          <div class="mb-3 wrap">
            <label class="form-label" for="form-cost">Nilai Aset</label>
            <div class="input-group">
              <div class="input-group-text">Rp</div>
              <input class="form-control" type="text" id="form-cost" name="acquisition_cost" placeholder="000" value="<?= number_format($assetFixedData['acquisition_cost'], 0, ',', '.') ?>" disabled></input>
            </div>
          </div>
          <div class="mb-3 wrap">
            <label class="form-label" for="formCheck2">Generate QR Code?&nbsp;
              <?php if ($qrCode['image'] != null && $qrCode['image'] != ''): ?>
                <span class="badge bg-success">Generated</span>
              <?php else: ?>
                <span class="badge bg-warning">Not Generated</span>
              <?php endif; ?>
            </label>
            <div class="square-switch">
              <input type="checkbox" id="square-switch1" switch="none" <?= $qrCode['image'] != null && $qrCode['image'] != '' ? 'checked' : '' ?> />
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
  $(document).ready(function() {
    $('#edit-asset-form').on('submit', function(e) {
      e.preventDefault();
      submitData();
    });

    function submitData() {
      var formData = new FormData();

      formData.append('_method', 'PUT');
      formData.append('id', $('input[name="id"]').val());
      formData.append('unit', $('#select-unit').val());
      formData.append('condition', $('#select-condition').val());
      formData.append('responsible_person', $('#form-responsible').val());
      formData.append('economic_life', $('#form-economic-life').val());
      formData.append('generate_qr', $('#square-switch1').is(':checked') ? 1 : 0);

      for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
      }

      $.ajax({
        url: '/asset-fixed/update/' + $('input[name="id"]').val(),
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