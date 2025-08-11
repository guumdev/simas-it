<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/partials/breadcrumb') ?>

<div class="row">
  <div class="col-9">
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped mb-0">
            <tbody>
              <tr>
                <td style="font-weight: bold; width: 20%">Kode Aset</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $datas['item_code'] ? ucfirst($datas['item_code']) : '-' ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold; width: 20%">Nama Aset</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $datas['item_name'] ? ucfirst($datas['item_name']) : '-' ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold; width: 20%">Jenis Perpindahan</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $datas['movement_type'] ? ucfirst($datas['movement_type']) : '-' ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold; width: 20%">Tanggal Perpindahan</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $datas['movement_date'] ? date('d F Y', strtotime($datas['movement_date'])) : '-' ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold; width: 20%">Lokasi Awal</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $datas['from_location_name'] ? ucfirst($datas['from_location_name']) : '-' ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold; width: 20%">Lokasi Akhir</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $datas['to_location_name'] ? ucfirst($datas['to_location_name']) : '-' ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold; width: 20%">Dilakukan oleh</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $datas['moved_by'] ? ucfirst($datas['moved_by']) : '-' ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold; width: 20%">Kondisi Sebelum Perpindahan</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $datas['condition_before'] ? ucfirst($datas['condition_before']) : '-' ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold; width: 20%">Kondisi Sesudah Perpindahan</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $datas['condition_after'] ? ucfirst($datas['condition_after']) : '-' ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold; width: 20%">Catatan</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $datas['notes'] ? htmlspecialchars($datas['notes']) : '-' ?></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="mt-3 d-flex gap-2 justify-content-end">
          <a href="/asset-maintenances" class="btn btn-secondary w-sm waves-effect waves-light">Kembali</a>
        </div>
      </div>
    </div>
  </div>
  <div class="col-3">
    <div class="card">
      <div class="card-header bg-primary-subtle">
        <h5 class="card-title mb-0">Foto Aset</h5>
      </div>
      <div class="card-body">
        <?php if (!empty($datas['item_image'])) : ?>
          <img src="<?= base_url('/uploads/images/barang/' . $datas['item_image']) ?>" alt="Item Image" class="img-thumbnail">
        <?php else: ?>
          <img src="https://placehold.co/600x400" alt="placeholder" class="img-thumbnail">
        <?php endif; ?>
      </div>
    </div>
    <div class="card">
      <div class="card-header bg-primary-subtle">
        <h5 class="card-title mb-0">QR Code</h5>
      </div>
      <div class="card-body">
        <?php if (!empty($datas['item_qr_image'])) : ?>
          <img src="<?= base_url('/uploads/qr_codes/' . $datas['item_qr_image']) ?>" alt="Item Image" class="img-thumbnail">
        <?php else: ?>
          <img src="https://placehold.co/500x500" alt="placeholder" class="img-thumbnail">
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('custom-js') ?>
<script>
  $(document).ready(function() {
    //
  });
</script>
<?= $this->endSection() ?>