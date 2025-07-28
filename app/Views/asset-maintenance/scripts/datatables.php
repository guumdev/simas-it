<script>
  $(document).ready(function() {
    var table = $('#maintenances-dtable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: '<?= base_url('datatables/asset-maintenances') ?>',
        data: function(d) {
          d.date_start_filter = $('#date-start-filter').val() ? moment($('#date-start-filter').val(), 'DD/MM/YYYY').format('YYYY-MM-DD') : '';
          d.date_end_filter = $('#date-end-filter').val() ? moment($('#date-end-filter').val(), 'DD/MM/YYYY').format('YYYY-MM-DD') : '';
        }
      },
      columns: [{
          data: 'id',
          name: 'id',
          searchable: false
        },
        {
          data: 'qr_codes',
          name: 'qr_codes'
        },
        {
          data: 'items_name',
          name: 'items_name'
        },
        {
          data: 'maintenance_location',
          name: 'maintenance_location'
        },
        {
          data: 'performed_by',
          name: 'performed_by'
        },
        {
          data: 'cost',
          name: 'cost',
          render: function(data) {
            let result = data.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            if (data % 1 === 0) {
              result = result.replace(/\.00$/, '');
            }
            return 'Rp' + result
          }
        },
        {
          data: 'maintenance_date',
          name: 'maintenance_date',
          render: function(data) {
            return moment(data).format('DD/MM/YYYY');
          }
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

    $('#btn-reset').addClass('disabled');

    $('#btn-reset').on('click', function() {
      $('#date-end-filter').val('');
      $('#date-start-filter').val('');
      $('#btn-reset').addClass('disabled');
      table.ajax.reload();
    });

    $('#date-start-filter, #date-end-filter').on('change', function() {
      $('#btn-reset').removeClass('disabled');
      table.ajax.reload();
    });
  });
</script>