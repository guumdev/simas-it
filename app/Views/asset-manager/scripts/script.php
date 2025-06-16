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
      $('#myLargeModalLabel').text('Buat Pengelola Aset');
      $('#amanager-modal').modal('show');
    });

    // open modal edit
    $(document).on('click', '.edit-btn', function(e) {
      e.preventDefault();
      showData(this);
    });

    // submit form
    $('#amanager-form').on('submit', function(e) {
      e.preventDefault();
      submitData();
    });

    // delete data
    $(document).on('click', '.delete-btn', function(e) {
      e.preventDefault();
      deleteData(this);
    });

    // reset form
    $('#amanager-modal').on('hidden.bs.modal', function(e) {
      document.getElementById('amanager-form').reset();
      window.ckeditorInstance.setData('');
      $('#label-amanager').addClass('visually-hidden');
      $('#form-amanager').addClass('visually-hidden');

      $('#amanager-form .form-control').removeClass('is-invalid is-valid');
      $('#amanager-form .invalid-feedback').remove();
    });

    function refreshManagerCounter() {
      $.get('asset-managers/counter', function(res) {
        $('#all-counter').text(res.allAssetManager);
        $('#deleted-counter').text(res.deletedAssetManager);
      });
    }

    // fn save amanager data
    function submitData() {
      let isValid = true;
      const id = $('#form-amanager-id').val();

      formData = {
        'code': $('input[name="code"]').val(),
        'name': $('input[name="name"]').val(),
        'description': $('#classic-editor').val(),
      };

      if (formData['code'] == '' || formData['name'] == '') {
        showToast('danger', 'Silakan isi semua input terlebih dahulu');
        isValid = false;
      }

      const url = id ?
        `<?= base_url('asset-managers/update') ?>/${id}` :
        `<?= base_url('asset-managers/create') ?>`;

      if (isValid) {
        $.ajax({
          url: url,
          method: 'POST',
          contentType: 'application/json',
          data: JSON.stringify(formData),
          success: function(response) {
            if (response.status == 'success') {
              showToast(response.toast.type, response.toast.message);
              $('#amanager-modal').modal('hide');
              $('#asset-manager-dtable').DataTable().ajax.reload(function() {
                refreshLocationCounter();
              });
              $('#form-amanager-id').val('');
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

    // fn show amanager data
    function showData(button) {
      const buttonClass = $(button).attr('class');
      const amanagerId = $(button).data('id');

      $.ajax({
        url: `<?= base_url('asset-managers') ?>/show/${amanagerId}`,
        method: 'GET',
        success: function(res) {
          // Set value ke form edit
          $('#form-amanager-id').val(res.data.id);
          $('#form-code').val(res.data.code);
          $('#form-name').val(res.data.name);
          $('#form-amanager').val(res.data.is_active);
          if (window.ckeditorInstance) {
            window.ckeditorInstance.setData(res.data.description);
          }

          // Ganti judul modal dan tampilkan
          $('#myLargeModalLabel').text('Edit Pengelola Aset');
          $('#amanager-modal').modal('show');

          // Show label amanager dan select amanager
          $('#label-amanager').removeClass('visually-hidden');
          $('#form-amanager').removeClass('visually-hidden');
        },
        error: function() {
          showToast('danger', 'Gagal mengambil data pengelola aset.');
        }
      });
    }

    // fn delete amanager data
    function deleteData(button) {
      const id = $(button).data('id');

      Swal.fire({
        title: 'Hapus',
        text: "Anda yakin ingin menghapus pengelola aset ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#34c38f',
        cancelButtonColor: '#f46a6a',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: `/asset-managers/delete/${id}`,
            method: 'DELETE',
            success: function(res) {
              if (res.status == 'success') {
                showToast(res.toast.type, res.toast.message);
                $('#asset-manager-dtable').DataTable().ajax.reload(function() {
                  refreshLocationCounter();
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