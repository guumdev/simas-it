<script>
  $(document).ready(function() {
    // datepicker
    $('#date-start-filter').datepicker({
      format: 'dd/mm/yyyy',
      autoclose: true
    });

    $('#date-end-filter').datepicker({
      format: 'dd/mm/yyyy',
      autoclose: true
    });

    // open view page
    $(document).on('click', '.view-btn', function(e) {
      e.preventDefault();
      window.location.href = `<?= base_url('asset-maintenances/show') ?>/${$(this).data('id')}`;
    });

    // open edit page
    $(document).on('click', '.edit-btn', function(e) {
      e.preventDefault();
      window.location.href = `<?= base_url('asset-maintenances/edit') ?>/${$(this).data('id')}`;
    });

    // delete data
    $(document).on('click', '.delete-btn', function(e) {
      e.preventDefault();
      deleteData(this);
    });

    // fn delete acategory data
    function deleteData(button) {
      const id = $(button).data('id');

      Swal.fire({
        title: 'Hapus',
        text: "Anda yakin ingin menghapus pemeliharaan aset ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#34c38f',
        cancelButtonColor: '#f46a6a',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: `/asset-maintenances/delete/${id}`,
            method: 'DELETE',
            success: function(res) {
              if (res.status == 'success') {
                showToast(res.toast.type, res.toast.message);
                $('#maintenances-dtable').DataTable().ajax.reload(function() {});
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