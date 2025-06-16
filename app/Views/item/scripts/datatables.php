<script>
  $(document).ready(function() {
    var table = $('#item-dtable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: '<?= base_url('datatables/items') ?>',
        data: function(d) {
          d.managers_filter = $('#managers-filter').val();
          d.date_filter = $('#date-filter').val();
        }
      },
      columns: [{
          data: 'id',
          name: 'id',
          orderable: false,
          searchable: false
        },
        {
          data: 'managers_name',
          name: 'm.name',
          searchable: true,
        },
        {
          data: 'categories_name',
          name: 'c.name',
          searchable: true,
          render: function(data, type, row, meta) {
            return `${row.categories_code} - ${data}`;
          }
        },
        {
          data: 'name',
          name: 'items.name',
          searchable: true,
        },
        {
          data: 'serial_number',
          name: 'items.serial_number',
          searchable: true,
        },
        {
          data: 'acquisition_date',
          name: 'items.acquisition_date',
          searchable: true,
          render: function(data) {
            return moment(data).format('DD/MM/YYYY');
          }
        },
        {
          data: 'image',
          name: 'image',
          render: function(data, type, row) {
            if (data && data !== '') {
              // return '<a href="' + window.location.origin + '/images/barang/' + data + '" target="_blank"><img src="' + window.location.origin + '/images/barang/' + data + '" alt="Asset Image" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px;"></a>';
              return '<a href="' + window.location.origin + '/images/barang/' + data + '" target="_blank" class="btn btn-sm btn-primary"><i class="uil-image"></i> Lihat foto</a>';
            } else {
              return '<span class="text-muted">No Image</span>';
            }
          },
          orderable: false
        },
        {
          data: null,
          orderable: false,
          searchable: false,
          render: function(data) {
            return `
                    <button type="button" class="btn btn-sm btn-primary view-btn" data-id="${data.id}">
                        <i class="uil-eye"></i> View
                    </button>
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

    $('#btn-reset').on('click', function() {
      $('#managers-filter').val('').trigger('change');
      $('#date-filter').val('');
      $('#btn-reset').addClass('btn-soft-danger');
      table.ajax.reload();
    });

    $('#date-filter, #managers-filter').on('change', function() {
      $('#btn-reset').removeClass('btn-soft-danger');
      table.ajax.reload();
    });
  });
</script>