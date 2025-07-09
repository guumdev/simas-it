<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/partials/breadcrumb') ?>

<form class="needs-validation" id="edit-item-form" novalidate>
  <?= csrf_field(); ?>
  <input type="hidden" name="id" value="<?= $item['id'] ?>">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="select-managers">Pengelola Aset <span class="text-danger">*</span></label>
              <select class="form-select" id="select-managers" name="asset_managers_id">
                <option selected disabled hidden>Pilih salah satu opsi</option>
                <?php if (empty($assetManagers)) : ?>
                  <option disabled>Tidak ada data</option>
                <?php else : ?>
                  <?php foreach ($assetManagers as $assetManager => $value) : ?>
                    <option value="<?= $value->id ?>" <?= ($value->id == $item['asset_managers_id']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars(strtoupper($value->name)) ?> - <?= $value->code ?>
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="select-categories">Kategori Aset <span class="text-danger">*</span></label>
              <select class="form-select" id="select-categories" name="asset_categories_id">
                <option selected disabled hidden>Pilih salah satu opsi</option>
                <?php if (!empty($assetCategories)) : ?>
                  <?php foreach ($assetCategories as $category) : ?>
                    <?php if ($category->asset_managers_id == $item['asset_managers_id']) : ?>
                      <option value="<?= $category->id ?>" <?= ($category->id == $item['asset_categories_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category->name) ?>
                      </option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="form-name">Nama Barang <span class="text-danger">*</span></label>
              <input class="form-control" type="text" id="form-name" name="name" placeholder="Masukkan nama barang" value="<?= htmlspecialchars($item['name']) ?>">
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="form-brand">Merek <span class="text-danger">*</span></label>
              <input class="form-control" type="text" id="form-brand" name="brand" placeholder="Masukkan merek barang" value="<?= htmlspecialchars($item['brand']) ?>">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="form-model">Model <span class="text-danger">*</span></label>
              <input class="form-control" type="text" id="form-model" name="model" placeholder="Masukkan model barang" value="<?= htmlspecialchars($item['model']) ?>">
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="form-serial">Nomor Seri <span class="text-danger">*</span></label>
              <input class="form-control" type="text" id="form-serial" name="serial_number" placeholder="Masukkan nomor seri barang" value="<?= htmlspecialchars($item['serial_number']) ?>">
            </div>
          </div>
          <div class="col-lg-12 mb-3">
            <label class="form-label" for="classic-editor">Spesifikasi</label>
            <textarea class="form-control" id="classic-editor" name="description" rows="6"><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="form-vendor">Vendor <span class="text-danger">*</span></label>
              <input class="form-control" type="text" id="form-vendor" name="vendor" placeholder="Masukkan nama vendor" value="<?= htmlspecialchars($item['vendor']) ?>">
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="form-acquisition-date">Tanggal Perolehan <span class="text-danger">*</span></label>
              <input class="form-control" type="date" id="form-acquisition-date" name="acquisition_date" placeholder="Masukkan tanggal perolehan barang" value="<?= $item['acquisition_date'] ?>">
            </div>
          </div>
          <div class="col-lg-12 mb-3">
            <label class="form-label" for="form-image">Foto Barang <small class="text-muted">(kosongkan jika tidak diubah)</small></label>
            <?php if (!empty($item['image'])) : ?>
              <div class="mb-2">
                <img src="<?= base_url('/uploads/images/barang/' . $item['image']) ?>" alt="Current Image" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                <p class="text-muted small mt-1">Foto saat ini: <?= htmlspecialchars($item['image']) ?></p>
              </div>
            <?php endif; ?>
            <input class="form-control" type="file" id="form-image" name="image" accept="image/*">
            <div class="form-text">
              <i class="uil-info-circle me-1"></i>
              Format yang didukung: .jpg, .jpeg .png (maksimal 2MB)
            </div>
          </div>
          <div class="mt-3 d-flex gap-2 justify-content-end">
            <a href="/items" class="btn btn-secondary w-sm waves-effect waves-light">Kembali</a>
            <button class="btn btn-primary w-sm waves-effect waves-light" type="submit">Update</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<?= $this->endSection() ?>

<?= $this->section('custom-js') ?>
<script>
  const allCategories = <?= json_encode($assetCategories) ?>;
  $(document).ready(function() {
    let ckeditorInstance;
    ClassicEditor
      .create(document.querySelector('#classic-editor'))
      .then(editor => {
        window.ckeditorInstance = editor;
      })
      .catch(error => {
        console.error(error);
      });

    $('#select-managers').on('change', function() {
      const selectedManagersId = $(this).val();
      const filtered = allCategories.filter(p => p.asset_managers_id == selectedManagersId);

      const $categorySelect = $('#select-categories');
      $categorySelect.prop('disabled', false);
      $categorySelect.empty().append('<option selected disabled hidden>Pilih salah satu opsi</option>');

      filtered.forEach(assetCategories => {
        $categorySelect.append(`<option value="${assetCategories.id}">${assetCategories.name}</option>`);
      });
    });

    $('#edit-item-form').on('submit', function(e) {
      e.preventDefault();
      submitData();
    });

    function submitData() {
      let isValid = true;

      const formData = new FormData();
      formData.append('_method', 'PUT');
      formData.append('id', $('input[name="id"]').val());
      formData.append('asset_managers_id', $('#select-managers').val());
      formData.append('asset_categories_id', $('#select-categories').val());
      formData.append('name', $('#form-name').val());
      formData.append('brand', $('#form-brand').val());
      formData.append('model', $('#form-model').val());
      formData.append('serial_number', $('#form-serial').val());
      formData.append('description', window.ckeditorInstance.getData());
      formData.append('vendor', $('#form-vendor').val());
      formData.append('acquisition_date', $('#form-acquisition-date').val());

      const imageFile = $('#form-image')[0].files[0];
      if (imageFile) {
        formData.append('image', imageFile);
      }

      $.ajax({
        url: '/items/update/' + $('input[name="id"]').val(),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.status == 'success') {
            showToast(response.toast.type, response.toast.message);
            setTimeout(function() {
              window.location.href = '/items';
            }, 2000);
          }
        },
        error: function(xhr, status, error) {
          if (xhr.status == 400) {
            console.log(error);
            showToast('danger', error);
          } else if (xhr.status == 500) {
            console.log(error + 'Internal server error.');
            showToast('danger', error + 'Internal server error.');
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