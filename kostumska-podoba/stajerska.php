<?php
declare(strict_types=1);

$pageTitle = 'Štajerska | Folklorna skupina Karavanke';
$basePrefix = '../';
$activeNav = 'kostumska-podoba';

require __DIR__ . '/../header.php';

$rows = [
    ['uploads/kostumska-podoba/stajerska/DSC_0352.jpg'],
    [
        'uploads/kostumska-podoba/stajerska/DSC_0329.jpg',
        'uploads/kostumska-podoba/stajerska/DSC_0346.jpg',
    ],
    ['uploads/kostumska-podoba/stajerska/DSC_0320.jpg'],
    ['uploads/kostumska-podoba/stajerska/DSC_0375b.jpg'],
];
?>
      <main class="domov wrapper flex-grow-1">
          <div class="container">
              <h1>Štajerska</h1>
              <p>Oblačenje Štajercev v 19. stoletju</p>

              <div class="gallery">
                  <?php foreach ($rows as $rowImgs): ?>
                      <?php $w = count($rowImgs) === 1 ? 1280 : 960; ?>
                      <div class="row align-items-start">
                          <?php foreach ($rowImgs as $rel): ?>
                              <div class="col"><?= renderGalleryItem($rel, 'Kostumska podoba - Štajerska', '../', $w) ?></div>
                          <?php endforeach; ?>
                      </div>
                  <?php endforeach; ?>
              </div>
          </div>
      </main>

      <?php require __DIR__ . '/../footer.php'; ?>
  </body>
</html>
