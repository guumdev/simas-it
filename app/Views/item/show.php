<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/partials/breadcrumb') ?>

<div class="row">
  <div class="col-3">
    <div class="card">
      <div class="card-body">
        <?php if (!empty($itemData['image'])) : ?>
          <img src="<?= base_url('/uploads/images/barang/' . $itemData['image']) ?>" alt="Item Image" class="img-thumbnail">
        <?php else: ?>
          <img src="https://placehold.co/600x400" alt="placeholder" class="img-thumbnail">
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-9">
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table mb-0">
            <tbody>
              <tr>
                <td style="font-weight: bold;">Pengelola Aset</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $assetManagerData['name'] ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold;">Kategori Aset</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $assetCategoryData['name'] ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold;">Nama Barang</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $itemData['name'] ?></td>
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
                <td style="font-weight: bold;">Tanggal Perolehan</td>
                <td><b>:</b>&nbsp;&nbsp;<?= date('d M Y', strtotime($itemData['acquisition_date'])) ?></td>
              </tr>
              <tr>
                <td style="font-weight: bold;">Spesifikasi</td>
                <td><b>:</b>&nbsp;&nbsp;<?= $itemData['description'] ?></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="mt-3 d-flex gap-2 justify-content-end">
          <a href="/items" class="btn btn-secondary w-sm waves-effect waves-light">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('custom-js') ?>

<?= $this->endSection() ?>