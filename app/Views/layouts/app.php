<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title><?= $webProperties['titleHeader'] ?? 'Dashboard' ?> | SIMAS IT</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="Assets Management System with Helpdesk at RSUD INDRAMAYU" name="description" />
  <meta content="RSUD INDRAMAYU" name="author" />
  <!-- App favicon -->
  <link rel="shortcut icon" href="<?= base_url('assets/images/favicon.ico'); ?>">

  <!-- Bootstrap Css -->
  <link href="<?= base_url('assets/css/bootstrap.min.css'); ?>" id="bootstrap-style" rel="stylesheet" type="text/css" />
  <!-- Icons Css -->
  <link href="<?= base_url('assets/css/icons.min.css'); ?>" rel="stylesheet" type="text/css" />
  <!-- Select2 -->
  <link href="<?= base_url('assets/libs/select2/css/select2.min.css'); ?>" rel="stylesheet" type="text/css" />
  <!-- DataTables -->
  <link href="<?= base_url('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet" type="text/css" />
  <link href="<?= base_url('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css'); ?>" rel="stylesheet" type="text/css" />
  <!-- Responsive datatable examples -->
  <link href="<?= base_url('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css'); ?>" rel="stylesheet" type="text/css" />
  <!-- Sweet Alert-->
  <link href="<?= base_url('assets/libs/sweetalert2/sweetalert2.min.css') ?>" rel="stylesheet" type="text/css" />
  <!-- App Css-->
  <link href="<?= base_url('assets/css/app.min.css'); ?>" id="app-style" rel="stylesheet" type="text/css" />
</head>


<body>
  <!-- <body data-layout="horizontal" data-topbar="colored"> -->

  <!-- Begin page -->
  <div id="layout-wrapper">
    <?= $this->include('layouts/partials/topbar'); ?>
    <?= $this->include('layouts/partials/sidebar'); ?>

    <div class="main-content">
      <!-- Toast container -->
      <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1080; margin-top: 60px;">
      </div><!-- End Toast container -->

      <!-- Page content -->
      <div class="page-content">
        <div class="container-fluid">
          <?= $this->renderSection('content'); ?>
        </div> <!-- container-fluid -->
      </div> <!-- End Page-content -->

      <?= $this->include('layouts/partials/footer'); ?>
    </div> <!-- end main content-->
  </div> <!-- END layout-wrapper -->

  <!-- JAVASCRIPT -->
  <script src="<?= base_url('assets/libs/jquery/jquery.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/metismenu/metisMenu.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/simplebar/simplebar.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/node-waves/waves.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/waypoints/lib/jquery.waypoints.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/jquery.counterup/jquery.counterup.min.js'); ?>"></script>

  <!-- plugins -->
  <script src="<?= base_url('assets/libs/select2/js/select2.min.js'); ?>"></script>
  <script src="<?= base_url('assets/js/pages/bootstrap-toasts.init.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js'); ?>"></script>

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

  <!-- Sweet Alert-->
  <script src="<?= base_url('assets/libs/sweetalert2/sweetalert2.min.js') ?>"></script>

  <!-- apexcharts -->
  <script src="<?= base_url('assets/libs/apexcharts/apexcharts.min.js'); ?>"></script>
  <script src="<?= base_url('assets/libs/moment/min/moment.min.js'); ?>"></script>

  <script src="<?= base_url('assets/js/pages/dashboard.init.js'); ?>"></script>

  <!-- App js -->
  <script src="<?= base_url('assets/js/app.js'); ?>"></script>

  <!-- Custom Js -->
  <?= $this->renderSection('custom-js'); ?>

  <script>
    function showToast(type, message) {
      const toastContainer = document.getElementById("toastContainer");

      // Create a new toast element
      const toast = document.createElement("div");
      toast.className = `toast text-white bg-${type} mb-2`;
      toast.setAttribute("role", "alert");
      toast.setAttribute("aria-live", "assertive");
      toast.setAttribute("aria-atomic", "true");

      toast.innerHTML = `
        <div class="d-flex">
          <div class="toast-body">
            ${message}
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      `;

      toastContainer.appendChild(toast);

      // Bootstrap Toast init + show
      const bsToast = new bootstrap.Toast(toast, {
        delay: 4000
      });
      bsToast.show();

      // Auto remove from DOM after hidden
      toast.addEventListener("hidden.bs.toast", () => {
        toast.remove();
      });
    }
  </script>

</body>

</html>