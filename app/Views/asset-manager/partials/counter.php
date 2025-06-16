<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="float-end mt-2">
          <i class="uil-file text-primary display-6"></i>
        </div>
        <div>
          <h4 class="mb-1 mt-1"><span id="all-counter" data-plugin="counterup"><?= $assetManagerCounter['allAssetManager'] ?></span></h4>
          <p class="text-muted mb-0">Semua Pengelola Aset</p>
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
          <h4 class="mb-1 mt-1"><span id="deleted-counter" data-plugin="counterup"><?= $assetManagerCounter['deletedAssetManager'] ?></span></h4>
          <p class="text-muted mb-0">Pengelola Aset Terhapus</p>
        </div>
      </div>
    </div>
  </div> <!-- end col-->
</div> <!-- end row-->