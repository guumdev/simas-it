<!doctype html>
<html lang="en">

<head>

  <meta charset="utf-8" />
  <title><?= $webProperties['titleHeader'] ?? 'Detail Aset' ?> | SIMAS IT</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="Assets Management System with Helpdesk at RSUD INDRAMAYU" name="description" />
  <meta content="RSUD INDRAMAYU" name="author" />
  <!-- App favicon -->
  <link rel="shortcut icon" href="<?= base_url('assets/images/favicon.ico'); ?>">

  <!-- Bootstrap Css -->
  <link href="<?= base_url('assets/css/bootstrap.min.css'); ?>" id="bootstrap-style" rel="stylesheet" type="text/css" />
  <!-- Icons Css -->
  <link href="<?= base_url('assets/css/icons.min.css'); ?>" rel="stylesheet" type="text/css" />
  <!-- DataTables -->
  <link href="<?= base_url('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet" type="text/css" />
  <link href="<?= base_url('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css'); ?>" rel="stylesheet" type="text/css" />
  <!-- Responsive datatable examples -->
  <link href="<?= base_url('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css'); ?>" rel="stylesheet" type="text/css" />
  <!-- App Css-->
  <link href="<?= base_url('assets/css/app.min.css'); ?>" id="app-style" rel="stylesheet" type="text/css" />

</head>

<body class="authentication-bg">
  <div class="container my-5 pt-sm-4">
    <a href="index.html" class="mb-5 d-block auth-logo">
      <img src="<?= base_url('assets/images/logo-dark.png'); ?>" alt="" height="32" class="logo logo-dark">
      <img src="<?= base_url('assets/images/logo-light.png'); ?>" alt="" height="32" class="logo logo-light">
    </a>
    <div class="row">
      <div class="card">
        <div class="card-body">
          <div class="row mb-4 align-items-center justify-content-center">
            <div class="col-md-7 text-center">
              <?php if (!empty($itemData['image'])) : ?>
                <img src="<?= base_url('/uploads/images/barang/' . $itemData['image']) ?>" alt="Item Image" class="img-thumbnail">
              <?php else: ?>
                <img src="https://placehold.co/720x480" alt="placeholder" class="img-thumbnail">
              <?php endif; ?>
            </div>
            <div class="col-md-2 text-center">
              <?php if (!empty($qrCode['image'])) : ?>
                <img src="<?= base_url('/uploads/qr_codes/' . $qrCode['image']) ?>" alt="Item Image" class="shadow" style="max-width: 100%; height: auto; border-radius: 8px;">
              <?php else: ?>
                <img src="https://placehold.co/500x500" alt="placeholder" class="img-thumbnail">
              <?php endif; ?>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-striped mb-6">
              <tbody>
                <tr>
                  <td style="font-weight: bold; width: 20%">Kode Aset</td>
                  <td><b>:</b>&nbsp;&nbsp;<?= $qrCode['content'] ? ucfirst($qrCode['content']) : '-' ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Nama Aset</td>
                  <td><b>:</b>&nbsp;&nbsp;<?= $itemData['name'] ?? '-' ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Kategori Aset</td>
                  <td><b>:</b>&nbsp;&nbsp;<?= $assetCategoryData['code'] ?> - <?= $assetCategoryData['name'] ?? '-' ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Pengelola Aset</td>
                  <td><b>:</b>&nbsp;&nbsp;<?= $assetManagerData['code'] ?> - <?= $assetManagerData['name']  ?? '-' ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Merek</td>
                  <td><b>:</b>&nbsp;&nbsp;<?= $itemData['brand'] ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Model</td>
                  <td><b>:</b>&nbsp;&nbsp;<?= $itemData['model'] ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Nomor Seri</td>
                  <td><b>:</b>&nbsp;&nbsp;<?= $itemData['serial_number'] ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Vendor</td>
                  <td><b>:</b>&nbsp;&nbsp;<?= $itemData['vendor'] ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Kondisi</td>
                  <td><b>:</b>&nbsp;&nbsp;<?= ucfirst($assetFixedData['condition']) ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Tanggal Perolehan</td>
                  <td><b>:</b>&nbsp;&nbsp;<?= date('d M Y', strtotime($itemData['acquisition_date'])) ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Lokasi Aset</td>
                  <td><b>:</b>&nbsp;&nbsp;<?= isset($assetLocationData['code'], $assetLocationData['name']) ? strtoupper($assetLocationData['code'] . ' - ' . $assetLocationData['name']) : '-' ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Penanggung Jawab Aset</td>
                  <td><b>:</b>&nbsp;&nbsp;<?= $assetFixedData['responsible_person'] ?? '-' ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Satuan</td>
                  <td><b>:</b>&nbsp;&nbsp;<?= ucfirst($assetFixedData['unit']) ?? '-' ?></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Nilai Aset</td>
                  <td><b>:</b>&nbsp;&nbsp;<?= isset($assetFixedData['acquisition_cost']) ? 'Rp ' . number_format($assetFixedData['acquisition_cost'], 0, ',', '.') : '-' ?></td>
                </tr>
                <!-- <tr>
                <td style="font-weight: bold;">Total Nilai Aset</td>
                <td><b>:</b>&nbsp;&nbsp;Rp -</td>
              </tr> -->
                <tr>
                  <td style="font-weight: bold;">Status Aset</td>
                  <td><b>:</b>&nbsp;&nbsp;-</td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Umur Ekonomis</td>
                  <td><b>:</b>&nbsp;&nbsp;<?= $assetFixedData['economic_life'] ?? '-' ?> Tahun</td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Masa Pemakaian</td>
                  <td id="usage-years"></td>
                </tr>
                <tr>
                  <td style="font-weight: bold;">Spesifikasi</td>
                  <td><b>:</b>&nbsp;&nbsp;<?= $itemData['description'] ?>
                </tr>
              </tbody>
            </table>
          </div>
          <h3 class="mt-3 mb-3"><b>Riwayat Perbaikan</b></h3>
          <table id="maintenances-dtable" class="table table-striped table-bordered" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead>
              <tr>
                <th>Kode Aset</th>
                <th>Nama</th>
                <th>Jenis Perbaikan</th>
                <th>Lokasi Perbaikan</th>
                <th>Biaya</th>
                <th>Durasi Perbaikan</th>
                <th>Tanggal Perbaikan</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- JAVASCRIPT -->
  <script src="<?= base_url('assets/libs/jquery/jquery.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/metismenu/metisMenu.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/simplebar/simplebar.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/node-waves/waves.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/waypoints/lib/jquery.waypoints.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/jquery.counterup/jquery.counterup.min.js'); ?>"></script>

  <!-- Datatable - Init -->
  <script src="<?= base_url('assets/js/pages/datatables.init.js'); ?>"></script>
  <!-- Datatable - Required datatable js -->
  <script src="<?= base_url('assets/libs/datatables.net/js/jquery.dataTables.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js'); ?>"></script>
  <!-- Datatable - Buttons examples -->
  <script src="<?= base_url('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/jszip/jszip.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/pdfmake/build/pdfmake.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/pdfmake/build/vfs_fonts.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/datatables.net-buttons/js/buttons.html5.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/datatables.net-buttons/js/buttons.print.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/datatables.net-buttons/js/buttons.colVis.min.js'); ?>"></script>
  <!-- Datatable - Responsive examples -->
  <script src="<?= base_url('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js'); ?>"></script>

  <!-- moment js -->
  <script src="<?= base_url('assets/libs/moment/min/moment.min.js'); ?>"></script>

  <!-- owl.carousel js -->
  <script src="<?= base_url('assets/libs/owl.carousel/owl.carousel.min.js'); ?>"></script>
  <!-- init js -->
  <script src="<?= base_url('assets/js/pages/auth-carousel.init.js'); ?>"></script>

  <script>
    $(document).ready(function() {
      const acquisitionDate = '<?= $itemData['acquisition_date'] ?>';
      const economicLife = '<?= $assetFixedData['economic_life'] ?>';

      const year = new Date(acquisitionDate).getFullYear();
      const currentYear = new Date().getFullYear();

      const usageYears = currentYear - year;
      const remainingYears = economicLife - usageYears;

      // console.log('Usage Years:', usageYears);
      // console.log('Remaining Years:', remainingYears);
      // console.log('Expired:', remainingYears <= 0);

      showUsage = `<b>:</b>&nbsp;&nbsp;${usageYears} Tahun`;

      $('#usage-years').html(showUsage);

      var table = $('#maintenances-dtable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: "/aset-berwujud/maintenance-dt/<?= $assetFixedData['id'] ?>",
          type: "GET",
        },
        columns: [{
            data: 'qr_codes',
            name: 'qr_codes'
          },
          {
            data: 'items_name',
            name: 'items_name'
          },
          {
            data: 'maintenance_type',
            name: 'maintenance_type',
            render: function(data) {
              return data.charAt(0).toUpperCase() + data.slice(1)
            }
          },
          {
            data: 'maintenance_location',
            name: 'maintenance_location',
            render: function(data) {
              return data.charAt(0).toUpperCase() + data.slice(1)
            }
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
            data: 'duration',
            name: 'duration',
            render: function(data) {
              return data + ' Hari';
            }
          },
          {
            data: 'maintenance_date',
            name: 'maintenance_date',
            render: function(data) {
              return moment(data).format('DD/MM/YYYY');
            }
          },
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

</body>

</html>