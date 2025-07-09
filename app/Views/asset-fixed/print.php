<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Print QR Codes | SIMAS IT</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="Assets Management System with Helpdesk at RSUD INDRAMAYU" name="description" />
  <meta content="RSUD INDRAMAYU" name="author" />
  <!-- App favicon -->
  <link rel="shortcut icon" href="<?= base_url('assets/images/favicon.ico'); ?>">

  <!-- Bootstrap Css -->
  <link href="<?= base_url('assets/css/bootstrap.min.css'); ?>" id="bootstrap-style" rel="stylesheet" type="text/css" />
  <!-- Icons Css -->
  <link href="<?= base_url('assets/css/icons.min.css'); ?>" rel="stylesheet" type="text/css" />
  <!-- App Css-->
  <link href="<?= base_url('assets/css/app.min.css'); ?>" id="app-style" rel="stylesheet" type="text/css" />

  <style>
    body {
      padding: 0.5rem;
    }

    .qr-card {
      border: 1px solid #dee2e6;
      border-radius: 0.25rem;
      width: 45mm;
      height: 55mm;
      page-break-inside: avoid;
    }

    .qr-image {
      width: 30mm;
      height: 30mm;
      object-fit: contain;
    }

    .qr-content {
      font-size: 0.65rem;
      line-height: 1.2;
      word-wrap: break-word;
      overflow-wrap: break-word;
    }

    /* Print styles */
    @media print {
      body {
        padding: 0.25rem !important;
      }

      .qr-card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
      }

      .qr-content {
        font-size: 0.6rem !important;
      }

      .container-fluid {
        padding: 0 !important;
      }

      .row {
        margin: 0 !important;
      }

      .col {
        padding: 0.125rem !important;
      }
    }

    @page {
      margin: 10mm;
      size: A4;
    }

    .page-break {
      page-break-before: always;
    }
  </style>
</head>

<body class="bg-white" onload="window.print()">
  <div class="container-fluid">
    <div class="row row-cols-auto justify-content-center g-2">
      <?php foreach ($qrCodes as $index => $qr): ?>
        <div class="col">
          <div class="qr-card p-2 text-center d-flex flex-column justify-content-between h-100">
            <div>
              <img src="<?= base_url('uploads/qr_codes/' . $qr['image']) ?>"
                alt="QR Code"
                class="qr-image mx-auto d-block">
            </div>
            <div class="qr-content mt-2 pt-1 border-top">
              <?= esc($qr['content']) ?>
            </div>
          </div>
        </div>

        <?php if (($index + 1) % 12 == 0 && $index + 1 < count($qrCodes)): ?>
          <div class="w-100 page-break"></div>
        <?php endif; ?>
      <?php endforeach; ?>
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

  <!-- owl.carousel js -->
  <script src="<?= base_url('assets/libs/owl.carousel/owl.carousel.min.js'); ?>"></script>
  <!-- init js -->
  <script src="<?= base_url('assets/js/pages/auth-carousel.init.js'); ?>"></script>

</body>

</html>