<?php
declare(strict_types=1);

$pageTitle = 'Koroška | Folklorna skupina Karavanke';
$basePrefix = '../';
$activeNav = 'kostumska-podoba';

require __DIR__ . '/../header.php';

$rows = [
    ['uploads/kostumska-podoba/koroska/DSC_0071.jpg'],
    ['uploads/kostumska-podoba/koroska/DSC_0063.jpg'],
    [
        'uploads/kostumska-podoba/koroska/DSC_0060.jpg',
        'uploads/kostumska-podoba/koroska/DSC_0026.jpg',
        'uploads/kostumska-podoba/koroska/DSC_0106.jpg',
    ],
    ['uploads/kostumska-podoba/koroska/DSC_2607.jpg'],
];
?>
      <main class="domov wrapper flex-grow-1">
          <div class="container">
              <h1>Koroška</h1>
              <p>Oblačenje Ziljanov v 19. stoletju</p>

              <div class="gallery">
                  <?php foreach ($rows as $rowImgs): ?>
                      <?php $w = count($rowImgs) === 1 ? 1280 : 960; ?>
                      <div class="row align-items-start">
                          <?php foreach ($rowImgs as $rel): ?>
                              <div class="col"><?= renderGalleryItem($rel, 'Kostumska podoba - Koroška', '../', $w) ?></div>
                          <?php endforeach; ?>
                      </div>
                  <?php endforeach; ?>
              </div>
          </div>
      </main>

      <?php require __DIR__ . '/../footer.php'; ?>
  </body>
</html>
