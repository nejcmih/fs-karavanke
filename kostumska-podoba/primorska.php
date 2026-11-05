<?php
declare(strict_types=1);

$pageTitle = 'Primorska | Folklorna skupina Karavanke';
$basePrefix = '../';
$activeNav = 'kostumska-podoba';

require __DIR__ . '/../header.php';

$rows = [
    ['uploads/kostumska-podoba/primorska/20191005_153035.jpg'],
    ['uploads/kostumska-podoba/primorska/20191005_153255.jpg'],
    ['uploads/kostumska-podoba/primorska/folklora-top-2965.jpg'],
    [
        'uploads/kostumska-podoba/primorska/DSC_0975.jpg',
        'uploads/kostumska-podoba/primorska/DSC_0945.jpg',
        'uploads/kostumska-podoba/primorska/DSC_0988.jpg',
    ],
    [
        'uploads/kostumska-podoba/primorska/DSC_3225.jpg',
        'uploads/kostumska-podoba/primorska/DSC_2976.jpg',
    ],
];
?>
      <main class="domov wrapper flex-grow-1">
          <div class="container">
              <h1>Primorska</h1>
              <p>Oblačenje v Istri in okolici Trsta v 19. stoletju</p>

              <div class="gallery">
                  <?php foreach ($rows as $rowImgs): ?>
                      <?php $w = count($rowImgs) === 1 ? 1280 : 960; ?>
                      <div class="row align-items-start">
                          <?php foreach ($rowImgs as $rel): ?>
                              <div class="col"><?= renderGalleryItem($rel, 'Kostumska podoba - Primorska', '../', $w) ?></div>
                          <?php endforeach; ?>
                      </div>
                  <?php endforeach; ?>
              </div>
          </div>
      </main>

      <?php require __DIR__ . '/../footer.php'; ?>
  </body>
</html>
