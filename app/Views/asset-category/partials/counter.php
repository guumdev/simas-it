<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="float-end mt-2">
          <i class="uil-file text-primary display-6"></i>
        </div>
        <div>
          <h4 class="mb-1 mt-1"><span id="all-counter" data-plugin="counterup"><?= $assetCategoryCounter['allAssetCategory'] ?></span></h4>
          <p class="text-muted mb-0">Semua Kategori Aset</p>
        </div>
      </div>
    </div>
  </div> <!-- end col-->

  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="float-end mt-2">
          <i class="uil-trash-alt text-danger display-6"></i>
        </div>
        <div>
          <h4 class="mb-1 mt-1"><span id="deleted-counter" data-plugin="counterup"><?= $assetCategoryCounter['deletedAssetCategory'] ?></span></h4>
          <p class="text-muted mb-0">Kategori Aset Terhapus</p>
        </div>
      </div>
    </div>
  </div> <!-- end col-->
</div> <!-- end row-->