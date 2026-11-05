<?php
declare(strict_types=1);

$pageTitle = 'Gorenjska | Folklorna skupina Karavanke';
$basePrefix = '../';
$activeNav = 'kostumska-podoba';

require __DIR__ . '/../header.php';

/**
 * @param list<list<string>> $rows
 */
function renderKostumGallery(array $rows, string $alt): void
{
    echo '<div class="gallery">';
    foreach ($rows as $rowImgs) {
        $thumbWidth = count($rowImgs) === 1 ? 1280 : 960;
        echo '<div class="row align-items-start">';
        foreach ($rowImgs as $rel) {
            echo '<div class="col">' . renderGalleryItem($rel, $alt, '../', $thumbWidth) . '</div>';
        }
        echo '</div>';
    }
    echo '</div>';
}
?>
      <main class="domov wrapper flex-grow-1">
          <div class="container">
              <h1>Gorenjska</h1>
              <p>Oblačenje premožnejšega kmečkega prebivalstva na Gorenjskem v 1. polovici 19. stoletja</p>
              <?php
              renderKostumGallery([
                  ['uploads/kostumska-podoba/gorenjska/DSC_0023.jpg'],
                  ['uploads/kostumska-podoba/gorenjska/DSC_0678.jpg'],
                  ['uploads/kostumska-podoba/gorenjska/DSC_0610.jpg'],
                  [
                      'uploads/kostumska-podoba/gorenjska/DSC_0669.jpg',
                      'uploads/kostumska-podoba/gorenjska/DSC_0431.jpg',
                  ],
              ], 'Kostumska podoba - Gorenjska');
              ?>

              <p>Oblačenje premožnejšega kmečkega prebivalstva na Gorenjskem ob koncu 19. stoletja</p>
              <?php
              renderKostumGallery([
                  [
                      'uploads/kostumska-podoba/gorenjska/DSC_0500.jpg',
                      'uploads/kostumska-podoba/gorenjska/DSC_0478.jpg',
                  ],
                  [
                      'uploads/kostumska-podoba/gorenjska/20191005_131000.jpg',
                      'uploads/kostumska-podoba/gorenjska/DSC_0599.jpg',
                  ],
                  [
                      'uploads/kostumska-podoba/gorenjska/DSC_0413b.jpg',
                      'uploads/kostumska-podoba/gorenjska/DSC_0526.jpg',
                  ],
                  ['uploads/kostumska-podoba/gorenjska/DSC_0542.jpg'],
              ], 'Kostumska podoba - Gorenjska');
              ?>

              <p>Vsakdanje oblačenje kmečkega prebivalstva na Gorenjskem v 1. polovici 20. stoletja</p>
              <?php
              renderKostumGallery([
                  ['uploads/kostumska-podoba/gorenjska/DSC_0236.jpg'],
                  [
                      'uploads/kostumska-podoba/gorenjska/DSC_0274.jpg',
                      'uploads/kostumska-podoba/gorenjska/P1040457.JPG',
                  ],
                  [
                      'uploads/kostumska-podoba/gorenjska/DSC_0139.jpg',
                      'uploads/kostumska-podoba/gorenjska/DSC_0349-001.JPG',
                  ],
                  ['uploads/kostumska-podoba/gorenjska/DSC_0086.JPG'],
              ], 'Kostumska podoba - Gorenjska');
              ?>
          </div>
      </main>

      <?php require __DIR__ . '/../footer.php'; ?>
  </body>
</html>
