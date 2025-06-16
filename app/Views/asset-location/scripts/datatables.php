<script>
  $(document).ready(function() {
    $('#asset-location-dtable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: '<?= base_url('datatables/asset-locations') ?>'
      },
      columns: [{
          data: '',
          name: '',
          orderable: false,
          searchable: false
        },
        {
          data: 'code',
          name: 'code',
        },
        {
          data: 'name',
          name: 'name',
        },
        {
          data: null,
          orderable: false,
          searchable: false,
          render: function(data) {
            return `
                    <button type="button" class="btn btn-sm btn-info edit-btn" data-id="${data.id}">
                        <i class="uil-edit"></i> Edit
                    </button>
                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="${data.id}">
                        <i class="uil-trash"></i> Delete
                    </button>
                `;
          }
        }
      ],
      "language": {
        "lengthMenu": "Tampilkan _MENU_ per halaman",
        "zeroRecords": "Data tidak ditemukan",
        "search": "Cari:",
        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        "infoEmpty": "Tidak ada data yang ditampilkan",
        "infoFiltered": "(difilter dari _MAX_ data)",
        "emptyTable": "Tabel kosong",
        "processing": "Sedang memproses...",
        "loadingRecords": "Sedang memuat data..."
      },
    });
  });
</script>