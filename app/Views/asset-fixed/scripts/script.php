<script>
  $(document).ready(function() {
    $('#locations-filter').select2({
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

    $('#year-filter').datepicker({
      format: 'yyyy',
      minViewMode: 2,
      startView: 2,
      autoclose: true
    });

    // open view page
    $(document).on('click', '.view-btn', function(e) {
      e.preventDefault();
      window.location.href = `<?= base_url('asset-fixed/show') ?>/${$(this).data('id')}`;
    });

    // open edit page
    $(document).on('click', '.edit-btn', function(e) {
      e.preventDefault();
      window.location.href = `<?= base_url('asset-fixed/edit') ?>/${$(this).data('id')}`;
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
  function refreshAssetFixedCounter() {
    // $.get('items/counter', function(res) {
    //   $('#all-counter').text(res.allItems);
    //   $('#programmer-counter').text(res.programmerItems);
    //   $('#hardware-counter').text(res.hardwareItems);
    //   $('#network-counter').text(res.networkItems);
    // });
  }

  // fn delete acategory data
  function deleteData(button) {
    const id = $(button).data('id');

    Swal.fire({
      title: 'Hapus',
      text: "Anda yakin ingin menghapus aset ini?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#34c38f',
      cancelButtonColor: '#f46a6a',
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal',
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: `/asset-fixed/delete/${id}`,
          method: 'DELETE',
          success: function(res) {
            if (res.status == 'success') {
              showToast(res.toast.type, res.toast.message);
              $('#asset-fixed-dtable').DataTable().ajax.reload(function() {
                refreshAssetFixedCounter();
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