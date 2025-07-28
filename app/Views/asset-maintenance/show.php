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
                <td><b>:</b>&nbsp;&nbsp;<?= $qrCode['content'] ? ucfirst($qrCode['content']) : '-' ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold; width: 20%">Nama Aset</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $item['name'] ? ucfirst($item['name']) : '-' ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold; width: 20%">Jenis Pemeliharaan</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $maintenance['maintenance_type'] ? ucfirst($maintenance['maintenance_type']) : '-' ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold; width: 20%">Tanggal Pemeliharaan</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $maintenance['maintenance_date'] ? date('d F Y', strtotime($maintenance['maintenance_date'])) : '-' ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold; width: 20%">Pemeliharaan Selanjutnya</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $maintenance['next_maintenance'] ? date('d F Y', strtotime($maintenance['next_maintenance'])) : '-' ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold; width: 20%">Lokasi</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $maintenance['maintenance_location'] ? ucwords($maintenance['maintenance_location']) : '-' ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold; width: 20%">Kondisi Perangkat</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $maintenance['device_status'] ? str_replace('_', ' ', ucfirst($maintenance['device_status'])) : '-' ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold; width: 20%">Dilakukan oleh</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $maintenance['performed_by'] ? ucwords($maintenance['performed_by']) : '-' ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold; width: 20%">Biaya</td>
                <td><b>:</b>&nbsp;&nbsp;Rp<?= $maintenance['cost'] ? number_format($maintenance['cost'], 2, ',', '.') : '-' ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold; width: 20%">Durasi</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $maintenance['duration'] ? ucwords($maintenance['duration']) : '-' ?> Hari</td>
              </tr>
              <tr>
                <td style="font-weight: bold; width: 20%">Deskripsi</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $maintenance['description'] ? $maintenance['description'] : '-' ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold; width: 20%">Tindakan</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $maintenance['maintenance_action'] ? $maintenance['maintenance_action'] : '-' ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold; width: 20%">Catatan</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $maintenance['notes'] ? $maintenance['notes'] : '-' ?></td>
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
        <?php if (!empty($item['image'])) : ?>
          <img src="<?= base_url('/uploads/images/barang/' . $item['image']) ?>" alt="Item Image" class="img-thumbnail">
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
        <?php if (!empty($qrCode['image'])) : ?>
          <img src="<?= base_url('/uploads/qr_codes/' . $qrCode['image']) ?>" alt="Item Image" class="img-thumbnail">
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
  $(document).ready(function() {});
</script>
<?= $this->endSection() ?>