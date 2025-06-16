<script>
  $(document).ready(function() {
    // editor input
    let ckeditorInstance;
    ClassicEditor
      .create(document.querySelector('#classic-editor'))
      .then(editor => {
        window.ckeditorInstance = editor;
      })
      .catch(error => {
        console.error(error);
      });

    // open modal create
    $('.btn-create').on('click', function(e) {
      e.preventDefault();
      $('#myLargeModalLabel').text('Buat Kategori Aset');
      $('#acategory-modal').modal('show');
    });

    // open modal edit
    $(document).on('click', '.edit-btn', function(e) {
      e.preventDefault();
      showData(this);
    });

    // submit form
    $('#acategory-form').on('submit', function(e) {
      e.preventDefault();
      submitData();
    });

    // delete data
    $(document).on('click', '.delete-btn', function(e) {
      e.preventDefault();
      deleteData(this);
    });

    // reset form
    $('#acategory-modal').on('hidden.bs.modal', function(e) {
      document.getElementById('acategory-form').reset();
      window.ckeditorInstance.setData('');
      $('#label-acategory').addClass('visually-hidden');
      $('#form-acategory').addClass('visually-hidden');

      $('#acategory-form .form-control').removeClass('is-invalid is-valid');
      $('#acategory-form .invalid-feedback').remove();
    });

    function refreshCategoryCounter() {
      $.get('asset-categories/counter', function(res) {
        $('#all-counter').text(res.allAssetCategory);
        $('#deleted-counter').text(res.deletedAssetCategory);
      });
    }

    // fn save acategory data
    function submitData() {
      let isValid = true;
      const id = $('#form-acategory-id').val();

      formData = {
        'code': $('input[name="code"]').val(),
        'asset_managers_id': $('select[name="manager_id"]').val(),
        'name': $('input[name="name"]').val(),
        'description': $('#classic-editor').val(),
      };

      if (formData['code'] == '' || formData['asset_managers_id'] == '' || formData['name'] == '') {
        showToast('danger', 'Silakan isi semua input terlebih dahulu');
        isValid = false;
      }

      const url = id ?
        `<?= base_url('asset-categories/update') ?>/${id}` :
        `<?= base_url('asset-categories/create') ?>`;

      if (isValid) {
        $.ajax({
          url: url,
          method: 'POST',
          contentType: 'application/json',
          data: JSON.stringify(formData),
          success: function(response) {
            if (response.status == 'success') {
              showToast(response.toast.type, response.toast.message);
              $('#acategory-modal').modal('hide');
              $('#asset-category-dtable').DataTable().ajax.reload(function() {
                refreshCategoryCounter();
              });
              $('#form-acategory-id').val('');
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
    }

    // fn show acategory data
    function showData(button) {
      const buttonClass = $(button).attr('class');
      const acategoryId = $(button).data('id');

      $.ajax({
        url: `<?= base_url('asset-categories') ?>/show/${acategoryId}`,
        method: 'GET',
        success: function(res) {
          // Set value ke form edit
          $('#form-acategory-id').val(res.data.id);
          $('#form-code').val(res.data.code);
          $('#form-manager').val(res.data.asset_managers_id);
          $('#form-name').val(res.data.name);
          if (window.ckeditorInstance) {
            window.ckeditorInstance.setData(res.data.description);
          }

          // Ganti judul modal dan tampilkan
          $('#myLargeModalLabel').text('Edit Kategori Aset');
          $('#acategory-modal').modal('show');

          // Show label acategory dan select acategory
          $('#label-acategory').removeClass('visually-hidden');
          $('#form-acategory').removeClass('visually-hidden');
        },
        error: function() {
          showToast('danger', 'Gagal mengambil data kategori aset.');
        }
      });
    }

    // fn delete acategory data
    function deleteData(button) {
      const id = $(button).data('id');

      Swal.fire({
        title: 'Hapus',
        text: "Anda yakin ingin menghapus kategori aset ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#34c38f',
        cancelButtonColor: '#f46a6a',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: `/asset-categories/delete/${id}`,
            method: 'DELETE',
            success: function(res) {
              if (res.status == 'success') {
                showToast(res.toast.type, res.toast.message);
                $('#asset-category-dtable').DataTable().ajax.reload(function() {
                  refreshCategoryCounter();
                });
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
                  console.log('Error: ' + res.message);
                  showToast('danger', res.message);
                } catch (e) {
                  console.log('Raw error: ' + xhr.responseText);
                }
              }
            },
          });
        }
      });
    }
  });
</script>