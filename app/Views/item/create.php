<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/partials/breadcrumb') ?>

<form class="needs-validation" id="create-item-form" novalidate>
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
              <label class="form-label" for="select-categories">Kategori Aset <span class="text-danger">*</span></label>
              <select class="form-select select2" id="select-categories" name="asset_categories_id" disabled>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="form-name">Nama Barang <span class="text-danger">*</span></label>
              <input class="form-control" type="text" id="form-name" name="name" placeholder="Masukkan nama barang"></input>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="form-brand">Merek <span class="text-danger">*</span></label>
              <input class="form-control" type="text" id="form-brand" name="brand" placeholder="Masukkan merek barang"></input>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="form-model">Model <span class="text-danger">*</span></label>
              <input class="form-control" type="text" id="form-model" name="model" placeholder="Masukkan model barang"></input>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="form-serial">Nomor Seri <span class="text-danger">*</span></label>
              <input class="form-control" type="text" id="form-serial" name="serial_number" placeholder="Masukkan nomor seri barang"></input>
            </div>
          </div>
          <div class="col-lg-12 mb-3">
            <label class="form-label" for="classic-editor">Spesifikasi</label>
            <textarea class="form-control" id="classic-editor" name="description" rows="6"></textarea>
          </div>
          <div class="row mb-3">
            <div class="col-lg-6">
              <label class="form-label" for="form-vendor">Vendor <span class="text-danger">*</span></label>
              <input class="form-control" type="text" id="form-vendor" name="vendor" placeholder="Masukkan nama vendor"></input>
            </div>
            <div class="col-lg-6">
              <label class="form-label" for="form-acquisition-date">Tanggal Perolehan <span class="text-danger">*</span></label>
              <input class="form-control" type="date" id="form-acquisition-date" name="acquisition_date" placeholder="Masukkan tanggal perolehan barang"></input>
            </div>
          </div>
          <div class="col-lg-12 mb-3">
            <label class="form-label" for="form-image">Foto Barang <small class="text-muted">(kosongkan jika tidak diisi)</small></label>
            <input class="form-control" type="file" id="form-image" name="image" accept="image/*">
            <div class="form-text">
              <i class="uil-info-circle me-1"></i>
              Format yang didukung: .jpg, .jpeg .png (maksimal 2MB)
            </div>
          </div>
          <div class="mt-3 d-flex gap-2 justify-content-end">
            <a href="/items" class="btn btn-secondary w-sm waves-effect waves-light">Kembali</a>
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

    $('#select-categories').select2({
      placeholder: 'Pilih Kategori',
      width: '100%',
      language: {
        noResults: function() {
          return "Tidak ada hasil ditemukan";
        }
      }
    });

    $('#select-managers').on('change', function() {
      const selectedManagersId = $(this).val();
      const filtered = allCategories.filter(p => p.asset_managers_id == selectedManagersId);

      const $categorySelect = $('#select-categories');

      // Solusi yang lebih aman - cek dengan try-catch
      try {
        if ($categorySelect.data('select2')) {
          $categorySelect.select2('destroy');
        }
      } catch (e) {
        // Abaikan error jika select2 belum diinisialisasi
      }

      // Reset element
      $categorySelect.prop('disabled', false).empty().append(
        '<option selected disabled hidden>Pilih Kategori</option>'
      );

      // Tambahkan option hasil filter
      filtered.forEach(assetCategories => {
        $categorySelect.append(
          `<option value="${assetCategories.id}">${assetCategories.name}</option>`
        );
      });

      // Inisialisasi ulang select2
      $categorySelect.select2({
        placeholder: 'Pilih Kategori',
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

    $('#create-item-form').on('submit', function(e) {
      e.preventDefault();
      submitData();
    });

    function submitData() {
      var formData = new FormData();

      formData.append('asset_managers_id', $('#select-managers').val());
      formData.append('asset_categories_id', $('#select-categories').val());
      formData.append('name', $('#form-name').val());
      formData.append('brand', $('#form-brand').val());
      formData.append('model', $('#form-model').val());
      formData.append('serial_number', $('#form-serial').val());
      formData.append('description', window.ckeditorInstance.getData());
      formData.append('vendor', $('#form-vendor').val());
      formData.append('acquisition_date', $('#form-acquisition-date').val());

      var imageFile = $('#form-image')[0].files[0];
      if (imageFile) {
        formData.append('image', imageFile);
      }

      console.log('Submitting form data:', formData);

      $.ajax({
        url: '/items/store',
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