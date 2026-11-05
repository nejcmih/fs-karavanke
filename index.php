<?php
declare(strict_types=1);

require_once __DIR__ . '/admin/includes/dbh.inc.php';
require_once __DIR__ . '/admin/includes/clanki-list.inc.php';

$pageTitle = 'Folklorna skupina Karavanke';
$basePrefix = '';
$activeNav = 'domov';

require __DIR__ . '/header.php';
?>
      <main class="homepage wrapper flex-grow-1">
          <div class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-inner">
                  <?php for ($i = 1; $i <= 6; $i++): ?>
                      <div class="carousel-item<?= $i === 1 ? ' active' : '' ?>" data-bs-interval="4000">
                          <?= renderProgressiveImg('uploads/carousel/carousel' . $i . '.jpg', 'Slika ' . $i, '', 1280, 'd-block w-100', $i === 1) ?>
                      </div>
                  <?php endfor; ?>
              </div>
          </div>

          <div class="domovVsebina container">
              <h1>Zadnje novice</h1>

              <?php
              renderArticleList($pdo, [
                  'excludeCategory' => 'napovednik',
                  'orderDir' => 'DESC',
                  'limit' => 3,
              ]);
              ?>
          </div>

          <div id="oDrustvu">
              <div class="container">
                  <div class="row align-items-start">
                      <div class="col-md-6" id="oDrustvuSlika">
                          <?= renderProgressiveImg('uploads/mladinska-skupina/IMG_0561.JPG', 'O društvu', '', 960) ?>
                      </div>
                      <div class="col-md-6" id="oDrustvuText">
                          <p><a href="./#oDrustvu">Slovenščina</a> | <a href="./?lang=en#oDrustvu">English</a></p>
                          <?php if (($_GET['lang'] ?? '') === 'en'): ?>
                              <h1>About the group</h1>
                              <p>The Karavanke Folklore Group comes from the small town of Tržič, situated in a narrow valley below the Karavanke Mountains in northern Slovenia along the border with Austria, a home to a long-standing shoemaking tradition.</p>
                              <p>The very first beginnings of the folklore group date back to the 1950s, while the group was formally established in 1966 at the time of Tržič’s twinning with the French town of Saint Marie aux Mines. At that time, the folklore group was composed of one group of adults under the leadership of Marjan Vodnjov, while the first children’s group was formed in 1972.</p>
                              <p>Today, the folklore group has about sixty members in total. All dancers, musicians and singers strive their best to recreate the Slovene folk tradition, thus ensuring continuation of Slovenia&#39;s cultural heritage for the benefit of both, the young and the old generation. Currently, one children&#39;s and one youth group, as well as folk singers, are active within the folklore group.</p>
                              <p><a href="o-nas/predstavitev?lang=en">You can read more about the group here.</a></p>
                          <?php else: ?>
                              <h1>O društvu</h1>
                              <p>Kulturno društvo Folklorna skupina Karavanke prihaja iz manjšega mesta Tržič, ki leži v tesni dolini pod Karavankami na severu Slovenije ob meji z Avstrijo, kjer je doma dolgoletna čevljarska tradicija.</p>
                              <p>Prvi začetki folklorne dejavnosti segajo v petdeseta leta 20. stoletja, skupina pa je bila ustanovljena leta 1966 ob pobratenju Tržiča s francoskim mestom Saint Marie aux Mines. Ob ustanovitvi je pod vodstvom Marjana Vodnjova delovala ena odrasla skupina, prva otroška pa se ji je pridružila že leta 1972.</p>
                              <p>Folklorna skupina Karavanke v svojem društvu združuje okoli 60 članov. Vsi plesalci, godci in pevci se trudijo čim bolje poustvarjati slovensko ljudsko izročilo in s tem skrbijo, da kulturna dediščina na Slovenskem ne bo pozabljena. Trenutno v društvu delujeta otroška in mladinska folklorna skupina ter pevci in pevke ljudskih pesmi.</p>
                              <p><a href="o-nas/predstavitev">Tukaj lahko preberete več o našem društvu.</a></p>
                          <?php endif; ?>
                      </div>
                  </div>
              </div>
          </div>
      </main>

      <?php require __DIR__ . '/footer.php'; ?>
  </body>
</html>
