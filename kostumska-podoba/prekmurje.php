<?php
declare(strict_types=1);

$pageTitle = 'Prekmurje | Folklorna skupina Karavanke';
$basePrefix = '../';
$activeNav = 'kostumska-podoba';

require __DIR__ . '/../header.php';

$rows = [
    [
        'uploads/kostumska-podoba/prekmurje/20191005_112322.jpg',
        'uploads/kostumska-podoba/prekmurje/DSC_0259.jpg',
    ],
    ['uploads/kostumska-podoba/prekmurje/folklora-top-2742.jpg'],
    ['uploads/kostumska-podoba/prekmurje/DSC_0284.jpg'],
    ['uploads/kostumska-podoba/prekmurje/DSC_0272.jpg'],
];
?>
      <main class="domov wrapper flex-grow-1">
          <div class="container">
              <h1>Prekmurje</h1>
              <p>Oblačenje Prekmurcev v 19. stoletju</p>

              <div class="gallery">
                  <?php foreach ($rows as $rowImgs): ?>
                      <?php $w = count($rowImgs) === 1 ? 1280 : 960; ?>
                      <div class="row align-items-start">
                          <?php foreach ($rowImgs as $rel): ?>
                              <div class="col"><?= renderGalleryItem($rel, 'Kostumska podoba - Prekmurje', '../', $w) ?></div>
                          <?php endforeach; ?>
                      </div>
                  <?php endforeach; ?>
              </div>
          </div>
      </main>

      <?php require __DIR__ . '/../footer.php'; ?>
  </body>
</html>
