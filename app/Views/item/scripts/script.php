<script>
  $(document).ready(function() {
    $('#managers-filter').select2({
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

    // open view page
    $(document).on('click', '.view-btn', function(e) {
      e.preventDefault();
      window.location.href = `<?= base_url('items/show') ?>/${$(this).data('id')}`;
    });

    // open edit page
    $(document).on('click', '.edit-btn', function(e) {
      e.preventDefault();
      window.location.href = `<?= base_url('items/edit') ?>/${$(this).data('id')}`;
    });

    // delete data
    $(document).on('click', '.delete-btn', function(e) {
      e.preventDefault();
      deleteData(this);
    });

    $('#excel_file').on('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        const fileInfo = `
        <div class="mt-2 small text-muted file-info">
          <i class="uil-file-alt me-1"></i>
          File: ${file.name} (${fileSize} MB)
        </div>
      `;

        $('.file-info').remove();

        $(this).parent().append(fileInfo);
      }
    });
  });

  // timeout alert
  setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    });
  }, 10000);

  // fn show upload content
  function showUploadContent() {
    document.getElementById('tableContent').style.display = 'none';
    document.getElementById('uploadContent').style.display = 'block';
  }

  // fn show table content
  function showTableContent() {
    document.getElementById('uploadContent').style.display = 'none';
    document.getElementById('tableContent').style.display = 'block';
    $('.file-info').remove();
    document.getElementById('excel_file').value = '';
  }

  // fn refresh item counter
  function refreshItemCounter() {
    $.get('items/counter', function(res) {
      $('#all-counter').text(res.allItems);
      $('#programmer-counter').text(res.programmerItems);
      $('#hardware-counter').text(res.hardwareItems);
      $('#network-counter').text(res.networkItems);
    });
  }

  // fn delete acategory data
  function deleteData(button) {
    const id = $(button).data('id');

    Swal.fire({
      title: 'Hapus',
      text: "Anda yakin ingin menghapus barang ini?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#34c38f',
      cancelButtonColor: '#f46a6a',
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal',
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: `/items/delete/${id}`,
          method: 'DELETE',
          success: function(res) {
            if (res.status == 'success') {
              showToast(res.toast.type, res.toast.message);
              $('#item-dtable').DataTable().ajax.reload(function() {
                refreshItemCounter();
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
</script>