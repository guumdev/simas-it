<script>
  $(document).ready(function() {
    var table = $("#movements-dtable").DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "<?= base_url('datatables/asset-movements') ?>",
        data: function(d) {
          d.date_start_filter = $("#date-start-filter").val() ?
            moment($("#date-start-filter").val(), "DD/MM/YYYY").format(
              "YYYY-MM-DD"
            ) :
            "";
          d.date_end_filter = $("#date-end-filter").val() ?
            moment($("#date-end-filter").val(), "DD/MM/YYYY").format(
              "YYYY-MM-DD"
            ) :
            "";
        },
      },
      columns: [{
          data: "id",
          name: "id",
          searchable: false,
        },
        {
          data: null,
          render: function(data) {
            return data.qr_codes + ' - ' + data.items_name
          }
        },
        {
          data: "from_location_id",
          name: "from_location_id",
        },
        {
          data: "to_location_id",
          name: "to_location_id",
        },
        {
          data: "movement_type",
          name: "movement_type",
          render: function(data) {
            return data.split(" ").map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(" ");
          }
        },
        {
          data: "moved_by",
          name: "moved_by",
          render: function(data) {
            return data.split(" ").map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(" ");
          }
        },
        {
          data: "movement_date",
          name: "movement_date",
          render: function(data) {
            return moment(data).format("DD/MM/YYYY");
          },
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

    $("#btn-reset").addClass("disabled");

    $("#btn-reset").on("click", function() {
      $("#date-end-filter").val("");
      $("#date-start-filter").val("");
      $("#btn-reset").addClass("disabled");
      table.ajax.reload();
    });

    $("#date-start-filter, #date-end-filter").on("change", function() {
      $("#btn-reset").removeClass("disabled");
      table.ajax.reload();
    });
  });
</script>