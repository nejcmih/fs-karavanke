<?php
declare(strict_types=1);

$pageTitle = 'Bela krajina | Folklorna skupina Karavanke';
$basePrefix = '../';
$activeNav = 'kostumska-podoba';

require __DIR__ . '/../header.php';

$rows = [
    [
        'uploads/kostumska-podoba/bela-krajina/DSC_0817.jpg',
        'uploads/kostumska-podoba/bela-krajina/DSC_0790.jpg',
    ],
    ['uploads/kostumska-podoba/bela-krajina/DSC_0809.jpg'],
    [
        'uploads/kostumska-podoba/bela-krajina/DSC_0799.jpg',
        'uploads/kostumska-podoba/bela-krajina/vkbk1.jpg',
    ],
    ['uploads/kostumska-podoba/bela-krajina/folklora-top-2865.jpg'],
    ['uploads/kostumska-podoba/bela-krajina/DSC_2850.jpg'],
];
?>
      <main class="domov wrapper flex-grow-1">
          <div class="container">
              <h1>Bela krajina</h1>
              <p>Oblačenje Belokranjcev v 19. stoletju</p>

              <div class="gallery">
                  <?php foreach ($rows as $rowImgs): ?>
                      <?php $w = count($rowImgs) === 1 ? 1280 : 960; ?>
                      <div class="row align-items-start">
                          <?php foreach ($rowImgs as $rel): ?>
                              <div class="col"><?= renderGalleryItem($rel, 'Kostumska podoba - Bela krajina', '../', $w) ?></div>
                          <?php endforeach; ?>
                      </div>
                  <?php endforeach; ?>
              </div>
          </div>
      </main>

      <?php require __DIR__ . '/../footer.php'; ?>
  </body>
</html>
