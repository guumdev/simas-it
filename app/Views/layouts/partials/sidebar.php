    <!-- ========== Left Sidebar Start ========== -->
    <div class="vertical-menu">

      <!-- LOGO -->
      <div class="navbar-brand-box">
        <a href="index.html" class="logo logo-dark">
          <span class="logo-sm">
            <img src="<?= base_url('assets/images/logo-sm.png'); ?>" alt="" height="22">
          </span>
          <span class="logo-lg">
            <img src="<?= base_url('assets/images/logo-dark.png'); ?>" alt="" height="20">
          </span>
        </a>

        <a href="index.html" class="logo logo-light">
          <span class="logo-sm">
            <img src="<?= base_url('assets/images/logo-sm.png'); ?>" alt="" height="22">
          </span>
          <span class="logo-lg">
            <img src="<?= base_url('assets/images/logo-light.png'); ?>" alt="" height="20">
          </span>
        </a>
      </div>

      <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect vertical-menu-btn">
        <i class="fa fa-fw fa-bars"></i>
      </button>

      <div data-simplebar class="sidebar-menu-scroll">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
          <!-- Left Menu Start -->
          <ul class="metismenu list-unstyled" id="side-menu">
            <li class="menu-title">Menu</li>

            <li>
              <a href="<?= base_url('dashboard'); ?>">
                <i class="uil-home-alt"></i>
                <span>Dashboard</span>
              </a>
            </li>

            <li>
              <a href="javascript: void(0);" class="has-arrow waves-effect">
                <i class="uil-book-alt"></i>
                <span>Master Data</span>
              </a>
              <ul class="sub-menu" aria-expanded="false">
                <li><a href="<?= base_url('/items'); ?>">Barang</a></li>
                <li><a href="<?= base_url('/asset-categories'); ?>">Kategori Aset</a></li>
                <li><a href="<?= base_url('/asset-managers'); ?>">Pengelola Aset</a></li>
                <li><a href="<?= base_url('/asset-locations'); ?>">Lokasi Aset</a></li>
              </ul>
            </li>

            <li>
              <a href="javascript: void(0);" class="has-arrow waves-effect">
                <i class="uil-cube"></i>
                <span>Asset Data</span>
              </a>
              <ul class="sub-menu" aria-expanded="false">
                <li><a href="<?= base_url('/asset-fixed'); ?>">Berwujud</a></li>
                <li><a href="assets.html">Habis Pakai</a></li>
                <li><a href="assets.html">Penghapusan Aset</a></li>
              </ul>
            </li>

            <li>
              <a href="javascript: void(0);" class="has-arrow waves-effect">
                <i class="uil-heart-rate"></i>
                <span>Monitoring Data</span>
              </a>
              <ul class="sub-menu" aria-expanded="false">
                <li><a href="assets.html">Audit</a></li>
                <li><a href="<?= base_url('/asset-movements') ?>">Perpindahan Aset</a></li>
                <li><a href="<?= base_url('/asset-maintenances') ?>">Pemeliharaan Aset</a></li>
              </ul>
            </li>

          </ul>
        </div>
        <!-- Sidebar -->
      </div>
    </div>
    <!-- Left Sidebar End -->