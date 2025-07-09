<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/partials/breadcrumb') ?>

<div class="row">
  <div class="col-3">
    <div class="card">
      <div class="card-header bg-primary-subtle">
        <h5 class="card-title mb-0">Foto Aset</h5>
      </div>
      <div class="card-body">
        <?php if (!empty($itemData['image'])) : ?>
          <img src="<?= base_url('/uploads/images/barang/' . $itemData['image']) ?>" alt="Item Image" class="img-thumbnail">
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
        <div class="mt-3 d-flex gap-2 justify-content-end">
          <a href="/asset-fixed" class="btn btn-secondary w-sm waves-effect waves-light">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('custom-js') ?>
<script>
  $(document).ready(function() {
    const acquisitionDate = '<?= $itemData['acquisition_date'] ?>';
    const economicLife = '<?= $assetFixedData['economic_life'] ?>';

    const year = new Date(acquisitionDate).getFullYear();
    const currentYear = new Date().getFullYear();

    const usageYears = currentYear - year;
    const remainingYears = economicLife - usageYears;

    console.log('Usage Years:', usageYears);
    console.log('Remaining Years:', remainingYears);
    console.log('Expired:', remainingYears <= 0);

    showUsage = `<b>:</b>&nbsp;&nbsp;${usageYears} Tahun`;

    $('#usage-years').html(showUsage);
  });
</script>
<?= $this->endSection() ?>