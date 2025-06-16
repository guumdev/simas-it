<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-flex align-items-center justify-content-between">
      <h4 class="mb-0"><?= $webProperties['titlePage'] ?? 'Page Title' ?></h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <?php foreach ($webProperties['breadcrumbs'] ?? [] as $breadcrumb): ?>
            <?php if (!empty($breadcrumb['url'])): ?>
              <li class="breadcrumb-item"><a href="<?= $breadcrumb['url']; ?>"><?= $breadcrumb['label']; ?></a></li>
            <?php else: ?>
              <li class="breadcrumb-item active"><?= $breadcrumb['label']; ?></li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ol>
      </div>
    </div>
  </div>
</div> <!-- End page title -->