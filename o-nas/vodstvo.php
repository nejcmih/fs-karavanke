<?php
declare(strict_types=1);

$pageTitle = 'Vodstvo | Folklorna skupina Karavanke';
$basePrefix = '../';
$activeNav = 'o-nas';

require __DIR__ . '/../header.php';
?>
      <main class="domov wrapper flex-grow-1">
          <div class="container">
              <h1>Vodstvo</h1>
              <div class="vodstvo row align-items-start">
                  <div class="col-md-6 col-lg-6 col-xl-6">
                      <h2>Saša Meglič</h2>
                      <p>Predsednica, umetniška vodja in vodja Mladinske folklorne skupine<br><a href="mailto:sasa.meglic@telemach.net">sasa.meglic@telemach.net</a></p>
                      <?= renderProgressiveImg('uploads/vodstvo/sasa.jpg', 'Saša Meglič', '../', 960, 'vodstvoSlika') ?>
                  </div>
                  <div class="col-md-6 col-lg-6 col-xl-6">
                      <h2>Pija Mejač</h2>
                      <p>Vodja Mladinske folklorne skupine ter pevk in pevcev ljudskih pesmi<br><a href="mailto:pija.mejac@gmail.com">pija.mejac@gmail.com</a></p>
                      <?= renderProgressiveImg('uploads/vodstvo/pija.jpg', 'Pija Mejač', '../', 960, 'vodstvoSlika') ?>
                  </div>
              </div>

              <div class="vodstvo row align-items-start">
                  <div class="col-md-6 col-lg-6 col-xl-6">
                      <h2>Dani Meglič</h2>
                      <p>Tajnica in garderoberka<br><a href="mailto:dani.meglic@gmail.com">dani.meglic@gmail.com</a></p>
                      <?= renderProgressiveImg('uploads/vodstvo/dani.jpg', 'Dani Meglič', '../', 960, 'vodstvoSlika') ?>
                  </div>
                  <div class="col-md-6 col-lg-6 col-xl-6">
                      <h2>Nika Krištof</h2>
                      <p>Vodja Mlajše otroške folklorne skupine<br><a href="mailto:nika.kristof@gmail.com">nika.kristof@gmail.com</a></p>
                      <?= renderProgressiveImg('uploads/vodstvo/nika.jpg', 'Nika Krištof', '../', 960, 'vodstvoSlika') ?>
                  </div>
              </div>

              <div class="vodstvo row align-items-start">
                  <div class="col-md-6 col-lg-6 col-xl-6">
                      <h2>Nika Buček</h2>
                      <p>Vodja Starejše otroške folklorne skupine<br><a href="mailto:bucek.nika@gmail.com">bucek.nika@gmail.com</a></p>
                  </div>
                  <div class="col"></div>
              </div>
          </div>
      </main>

      <?php require __DIR__ . '/../footer.php'; ?>
  </body>
</html>
