<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.inc.php';
requireAuth('login');
require_once __DIR__ . '/includes/dbh.inc.php';
require_once __DIR__ . '/includes/functions.inc.php';
require_once __DIR__ . '/includes/image-thumb.inc.php';

$categories = getArticleCategories();

$totalArticles = (int) $pdo->query('SELECT COUNT(*) FROM clanki')->fetchColumn();

$yearStmt = $pdo->prepare('SELECT COUNT(*) FROM clanki WHERE YEAR(datum) = :yr');
$yearStmt->execute([':yr' => (int) date('Y')]);
$thisYearArticles = (int) $yearStmt->fetchColumn();

$upcomingStmt = $pdo->prepare('SELECT COUNT(*) FROM clanki WHERE kategorija = :kat');
$upcomingStmt->execute([':kat' => 'napovednik']);
$napovednikCount = (int) $upcomingStmt->fetchColumn();

$userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();

$recentStmt = $pdo->query(
    'SELECT ID, naslov, kategorija, kraj, DATE_FORMAT(datum, "%d. %m. %Y") AS lepdatum, naslovnaslika
     FROM clanki
     ORDER BY ID DESC
     LIMIT 6'
);
$recentArticles = $recentStmt->fetchAll();

$catCountsStmt = $pdo->query('SELECT kategorija, COUNT(*) AS cnt FROM clanki GROUP BY kategorija ORDER BY cnt DESC');
$categoryCounts = $catCountsStmt->fetchAll();

$adminPageTitle = 'Nadzorna plošča';
$adminActive = 'dashboard';
require __DIR__ . '/header.php';
?>
  <body class="d-flex flex-column min-vh-100">
      <?php require __DIR__ . '/meni.php'; ?>

      <main class="admin-main flex-grow-1">
          <div class="container">
              <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                  <div>
                      <h1 class="h3 fw-bold mb-1">Pozdravljen, <?= escapeHtml((string) ($_SESSION['useruid'] ?? '')) ?>!</h1>
                      <p class="text-secondary mb-0">Uredniška konzola Kulturnega društva Folklorna skupina Karavanke</p>
                  </div>
                  <div class="d-flex gap-2">
                      <a href="clanki" class="btn btn-outline-secondary">
                          <i class="fa-solid fa-list-ul me-1"></i>Vsi članki
                      </a>
                      <a href="dodaj" class="btn btn-fsk px-3">
                          <i class="fa-solid fa-plus me-1"></i>Nov članek
                      </a>
                  </div>
              </div>

              <!-- KPI Stat Cards -->
              <div class="row g-3 mb-4">
                  <div class="col-sm-6 col-xl-3">
                      <div class="admin-stat-card d-flex align-items-center justify-content-between">
                          <div>
                              <div class="text-secondary small fw-medium">Skupaj člankov</div>
                              <div class="h3 fw-bold mb-0 mt-1"><?= $totalArticles ?></div>
                          </div>
                          <div class="admin-stat-icon bg-primary bg-opacity-10 text-primary">
                              <i class="fa-regular fa-newspaper"></i>
                          </div>
                      </div>
                  </div>
                  <div class="col-sm-6 col-xl-3">
                      <div class="admin-stat-card d-flex align-items-center justify-content-between">
                          <div>
                              <div class="text-secondary small fw-medium">Objavljeno v <?= (int) date('Y') ?></div>
                              <div class="h3 fw-bold mb-0 mt-1"><?= $thisYearArticles ?></div>
                          </div>
                          <div class="admin-stat-icon bg-success bg-opacity-10 text-success">
                              <i class="fa-regular fa-calendar-check"></i>
                          </div>
                      </div>
                  </div>
                  <div class="col-sm-6 col-xl-3">
                      <div class="admin-stat-card d-flex align-items-center justify-content-between">
                          <div>
                              <div class="text-secondary small fw-medium">V napovedniku</div>
                              <div class="h3 fw-bold mb-0 mt-1"><?= $napovednikCount ?></div>
                          </div>
                          <div class="admin-stat-icon bg-warning bg-opacity-10 text-warning">
                              <i class="fa-solid fa-bullhorn"></i>
                          </div>
                      </div>
                  </div>
                  <div class="col-sm-6 col-xl-3">
                      <div class="admin-stat-card d-flex align-items-center justify-content-between">
                          <div>
                              <div class="text-secondary small fw-medium">Uredniški računi</div>
                              <div class="h3 fw-bold mb-0 mt-1"><?= $userCount ?></div>
                          </div>
                          <div class="admin-stat-icon bg-info bg-opacity-10 text-info">
                              <i class="fa-solid fa-user-shield"></i>
                          </div>
                      </div>
                  </div>
              </div>

              <!-- Quick Actions -->
              <div class="row g-3 mb-4">
                  <div class="col-md-4">
                      <a href="dodaj" class="admin-quick-action">
                          <div class="admin-stat-icon bg-primary bg-opacity-10 text-primary flex-shrink-0">
                              <i class="fa-solid fa-pen-nib"></i>
                          </div>
                          <div>
                              <div class="fw-semibold">Dodaj nov članek</div>
                              <div class="small text-secondary">Oblikuj besedilo, podnaslove in galerije</div>
                          </div>
                      </a>
                  </div>
                  <div class="col-md-4">
                      <a href="clanki" class="admin-quick-action">
                          <div class="admin-stat-icon bg-success bg-opacity-10 text-success flex-shrink-0">
                              <i class="fa-solid fa-folder-open"></i>
                          </div>
                          <div>
                              <div class="fw-semibold">Upravljaj članke</div>
                              <div class="small text-secondary">Išči, uredi ali izbriši obstoječe objave</div>
                          </div>
                      </a>
                  </div>
                  <div class="col-md-4">
                      <a href="signup" class="admin-quick-action">
                          <div class="admin-stat-icon bg-secondary bg-opacity-10 text-secondary flex-shrink-0">
                              <i class="fa-solid fa-user-plus"></i>
                          </div>
                          <div>
                              <div class="fw-semibold">Uredniki in dostop</div>
                              <div class="small text-secondary">Dodaj novega avtorja ali urednika</div>
                          </div>
                      </a>
                  </div>
              </div>

              <div class="row g-4">
                  <!-- Recent Articles -->
                  <div class="col-lg-8">
                      <div class="admin-card p-4">
                          <div class="d-flex justify-content-between align-items-center mb-3">
                              <h2 class="h5 fw-bold mb-0">Zadnje dodani članki</h2>
                              <a href="clanki" class="small text-decoration-none fw-medium">Prikaži vse &rarr;</a>
                          </div>

                          <div class="table-responsive">
                              <table class="table align-middle mb-0">
                                  <thead class="table-light">
                                      <tr>
                                          <th style="width: 72px;">Slika</th>
                                          <th>Naslov</th>
                                          <th>Kategorija</th>
                                          <th>Datum</th>
                                          <th class="text-end">Dejanja</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <?php foreach ($recentArticles as $row): ?>
                                          <?php
                                          $aid = (int) $row['ID'];
                                          $cover = trim((string) ($row['naslovnaslika'] ?? ''));
                                          $catKey = (string) $row['kategorija'];
                                          $catLabel = $categories[$catKey] ?? $catKey;
                                          ?>
                                          <tr>
                                              <td>
                                                  <?php if ($cover !== ''): ?>
                                                      <img src="<?= escapeHtml(getThumbUrl('clanki/' . $aid . '/' . $cover, 960, '../')) ?>" alt="" class="admin-thumb" loading="lazy">
                                                  <?php else: ?>
                                                      <span class="admin-thumb-placeholder"><i class="fa-regular fa-image"></i></span>
                                                  <?php endif; ?>
                                              </td>
                                              <td>
                                                  <div class="fw-semibold"><?= escapeHtml((string) $row['naslov']) ?></div>
                                                  <?php if (trim((string) ($row['kraj'] ?? '')) !== ''): ?>
                                                      <div class="small text-secondary"><i class="fa-solid fa-location-dot me-1"></i><?= escapeHtml((string) $row['kraj']) ?></div>
                                                  <?php endif; ?>
                                              </td>
                                              <td><span class="badge rounded-pill text-bg-light border text-secondary fw-medium"><?= escapeHtml($catLabel) ?></span></td>
                                              <td class="text-nowrap small text-secondary"><?= escapeHtml((string) $row['lepdatum']) ?></td>
                                              <td class="text-end text-nowrap">
                                                  <a href="../clanki/<?= $aid ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary" title="Ogled članka">
                                                      <i class="fa-solid fa-eye"></i>
                                                  </a>
                                                  <a href="uredi?id=<?= $aid ?>" class="btn btn-sm btn-outline-primary ms-1">
                                                      <i class="fa-solid fa-pen me-1"></i>Uredi
                                                  </a>
                                              </td>
                                          </tr>
                                      <?php endforeach; ?>
                                  </tbody>
                              </table>
                          </div>
                      </div>
                  </div>

                  <!-- Category Breakdown -->
                  <div class="col-lg-4">
                      <div class="admin-card p-4">
                          <h2 class="h5 fw-bold mb-3">Članki po kategorijah</h2>
                          <ul class="list-group list-group-flush">
                              <?php foreach ($categoryCounts as $cRow): ?>
                                  <?php
                                  $cKey = (string) $cRow['kategorija'];
                                  $cName = $categories[$cKey] ?? $cKey;
                                  $cNum = (int) $cRow['cnt'];
                                  ?>
                                  <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                      <a href="clanki?kategorija=<?= escapeHtml(rawurlencode($cKey)) ?>" class="text-decoration-none text-dark">
                                          <?= escapeHtml($cName) ?>
                                      </a>
                                      <span class="badge rounded-pill bg-secondary bg-opacity-10 text-dark"><?= $cNum ?></span>
                                  </li>
                              <?php endforeach; ?>
                          </ul>
                      </div>
                  </div>
              </div>
          </div>
      </main>
  </body>
</html>
