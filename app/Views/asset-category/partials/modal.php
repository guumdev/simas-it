<!-- Modal -->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="acategory-modal">
  <div class=" modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myLargeModalLabel">Title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <div class="modal-body">
        <form class="needs-validation" id="acategory-form" novalidate>
          <?= csrf_field(); ?>
          <input type="hidden" id="form-acategory-id" name="id">
          <div class="col-lg-12 mb-3">
            <label class="form-label" for="form-code">Kode <span class="text-danger">*</span></label>
            <input class="form-control" type="text" id="form-code" name="code" placeholder="Masukkan kode kategori aset"></input>
          </div>
          <div class="col-lg-12 mb-3">
            <label class="form-label" for="form-name">Nama <span class="text-danger">*</span></label>
            <input class="form-control" type="text" id="form-name" name="name" placeholder="Masukkan nama kategori aset"></input>
          </div>
          <div class="col-lg-12 mb-3">
            <label class="form-label" for="form-manager" id="label-manager">Pengelola Aset</label>
            <select class="form-select" id="form-manager" name="manager_id">
              <option value="" disabled>Pilih pengelola aset</option>
              <?php foreach ($assetManagerData as $data) : ?>
                <option value="<?= $data['id']; ?>"><?= $data['name']; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-lg-12">
            <label class="form-label" for="classic-editor">Deskripsi</label>
            <textarea class="form-control" id="classic-editor" name="description" rows="8"></textarea>
          </div>
          <div class="mt-3 d-flex gap-2 justify-content-end">
            <a class="btn btn-secondary w-sm waves-effect waves-light" data-bs-dismiss="modal">Kembali</a>
            <button class="btn btn-primary w-sm waves-effect waves-light" type="submit">Simpan</button>
          </div>
        </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->