<script>
  $(document).ready(function() {
    var table = $('#asset-fixed-dtable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: '<?= base_url('datatables/asset-fixed') ?>',
        data: function(d) {
          d.locations_filter = $('#locations-filter').val();
          d.year_filter = $('#year-filter').val();
        }
      },
      columns: [{
        data: null,
        orderable: false,
        searchable: false,
        render: function(data, type, row) {
          return `<input type="checkbox" class="table-select" value="${row.id}" qr="${row.qr_code_id}" qr-image="${row.qr_image}">`;
        }
      }, {
        data: 'id',
        orderable: true,
        searchable: false,
      }, {
        data: 'qr_content',
        searchable: true,
        render: function(data, type, row) {
          return data ? data : '<span class="text-muted">-</span>';
        }
      }, {
        data: 'items_name',
        orderable: true,
        searchable: true,
      }, {
        data: 'locations_name',
        orderable: true,
        searchable: true,
      }, {
        data: 'condition',
        orderable: true,
        searchable: true,
      }, {
        data: 'item_acquisition_date',
        orderable: true,
        searchable: true,
        render: function(data) {
          return moment(data).format('DD/MM/YYYY');
        }
      }, {
        data: 'qr_image',
        searchable: false,
        orderable: false,
        render: function(data, type, row) {
          if (data != null) {
            return `<img src="${window.location.origin}/uploads/qr_codes/${data}" 
                alt="QR Code" 
                class="qr-image" 
                style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; cursor: pointer;"
                data-qr-image="${data}"
                data-asset-code="${row.qr_codes}">`;
          } else {
            return '<span class="badge bg-warning">Not Generated</span>';
          }
        }
      }, {
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
      }],
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
      $('#locations-filter').val('').trigger('change');
      $('#year-filter').val('');
      $('#btn-reset').addClass('disabled');
      table.ajax.reload();
    });

    $('#year-filter, #locations-filter').on('change', function() {
      $('#btn-reset').removeClass('disabled');
      table.ajax.reload();
    });

    $('#asset-fixed-dtable').on('click', '.qr-image', function() {
      const qrImage = $(this).data('qr-image');
      const assetCode = $(this).data('asset-code');

      // Debug: cek apakah data ada
      console.log('QR Image data:', qrImage);
      console.log('Asset Code:', assetCode);

      if (qrImage) {
        // Set QR image source
        const qrImageSrc = window.location.origin + '/uploads/qr_codes/' + qrImage;
        $('#modalQrImage').attr('src', qrImageSrc);
        $('#modalAssetCode').text(assetCode);

        // Show modal
        $('#qrModal').modal('show');
      }
    });
  });
</script>