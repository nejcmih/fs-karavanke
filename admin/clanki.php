<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.inc.php';
requireAuth('login');
require_once __DIR__ . '/includes/dbh.inc.php';
require_once __DIR__ . '/includes/functions.inc.php';
require_once __DIR__ . '/includes/image-thumb.inc.php';

$categories = getArticleCategories();
$selectedCategory = isset($_GET['kategorija']) ? trim((string) $_GET['kategorija']) : '';

$stmt = $pdo->query(
    'SELECT ID, naslov, kategorija, kraj, datum, DATE_FORMAT(datum, "%d. %m. %Y") AS lepdatum, preview, naslovnaslika
     FROM clanki
     ORDER BY ID DESC'
);
$rows = $stmt->fetchAll();
$csrfToken = getCsrfToken();

$status = isset($_GET['error']) ? (string) $_GET['error'] : '';
$highlightId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$adminPageTitle = 'Vsi članki';
$adminActive = 'clanki';
require __DIR__ . '/header.php';
?>
  <body class="d-flex flex-column min-vh-100">
      <?php require __DIR__ . '/meni.php'; ?>

      <main class="admin-main flex-grow-1">
          <div class="container">
              <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                  <div>
                      <h1 class="h3 fw-bold mb-1">Upravljanje člankov</h1>
                      <p class="text-secondary mb-0">Skupaj <?= count($rows) ?> objav v arhivu spletne strani</p>
                  </div>
                  <a href="dodaj" class="btn btn-fsk px-3">
                      <i class="fa-solid fa-plus me-1"></i>Dodaj nov članek
                  </a>
              </div>

              <?php if ($status === 'added'): ?>
                  <div class="alert alert-success d-flex flex-wrap align-items-center justify-content-between gap-2">
                      <div><i class="fa-solid fa-circle-check me-2"></i>Nov članek je bil uspešno objavljen!</div>
                      <?php if (is_int($highlightId) && $highlightId > 0): ?>
                          <a href="../clanki/<?= $highlightId ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-success">
                              <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Poglej objavljen članek
                          </a>
                      <?php endif; ?>
                  </div>
              <?php elseif ($status === 'edited'): ?>
                  <div class="alert alert-success d-flex flex-wrap align-items-center justify-content-between gap-2">
                      <div><i class="fa-solid fa-circle-check me-2"></i>Spremembe članka so bile uspešno shranjene!</div>
                      <?php if (is_int($highlightId) && $highlightId > 0): ?>
                          <a href="../clanki/<?= $highlightId ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-success">
                              <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Poglej članek
                          </a>
                      <?php endif; ?>
                  </div>
              <?php elseif ($status === 'deleted'): ?>
                  <div class="alert alert-warning">
                      <i class="fa-solid fa-trash-can me-2"></i>Članek in njegove pripadajoče slike so bili izbrisani.
                  </div>
              <?php elseif ($status === 'csrf'): ?>
                  <div class="alert alert-danger">
                      <i class="fa-solid fa-triangle-exclamation me-2"></i>Seja je potekla ali pa je varnostni žeton neveljaven. Poskusi znova.
                  </div>
              <?php elseif ($status === 'stmtfailed'): ?>
                  <div class="alert alert-danger">
                      <i class="fa-solid fa-circle-exclamation me-2"></i>Napaka pri izvedbi ukaza v bazi. Poskusi znova.
                  </div>
              <?php endif; ?>

              <div class="admin-card p-3 p-md-4">
                  <!-- Search & Category Filter Bar -->
                  <div class="row g-2 align-items-center mb-3">
                      <div class="col-md-6">
                          <div class="input-group">
                              <span class="input-group-text bg-white text-secondary"><i class="fa-solid fa-magnifying-glass"></i></span>
                              <input type="search" id="articleSearchInput" class="form-control" placeholder="Išči po naslovu, kraju ali vsebini…">
                          </div>
                      </div>
                      <div class="col-md-4">
                          <select id="articleCategoryFilter" class="form-select">
                              <option value="">Vse kategorije</option>
                              <?php foreach ($categories as $catKey => $catLabel): ?>
                                  <option value="<?= escapeHtml($catKey) ?>"<?= $selectedCategory === $catKey ? ' selected' : '' ?>>
                                      <?= escapeHtml($catLabel) ?>
                                  </option>
                              <?php endforeach; ?>
                          </select>
                      </div>
                      <div class="col-md-2 text-md-end small text-secondary">
                          Prikazanih: <strong id="visibleArticlesCount"><?= count($rows) ?></strong>
                      </div>
                  </div>

                  <div class="table-responsive">
                      <table class="table table-hover align-middle mb-0" id="articlesTable">
                          <thead class="table-light">
                              <tr>
                                  <th style="width: 60px;">ID</th>
                                  <th style="width: 76px;">Slika</th>
                                  <th>Naslov in povzetek</th>
                                  <th>Kategorija</th>
                                  <th>Datum</th>
                                  <th class="text-end">Dejanja</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php foreach ($rows as $row): ?>
                                  <?php
                                  $articleId = (int) $row['ID'];
                                  $catKey = (string) $row['kategorija'];
                                  $catLabel = $categories[$catKey] ?? $catKey;
                                  $cover = trim((string) ($row['naslovnaslika'] ?? ''));
                                  $kraj = trim((string) ($row['kraj'] ?? ''));
                                  $plainPreview = trim(strip_tags((string) ($row['preview'] ?? '')));
                                  ?>
                                  <tr data-category="<?= escapeHtml($catKey) ?>">
                                      <td class="text-secondary small">#<?= $articleId ?></td>
                                      <td>
                                          <?php if ($cover !== ''): ?>
                                              <img src="<?= escapeHtml(getThumbUrl('clanki/' . $articleId . '/' . $cover, 960, '../')) ?>" alt="" class="admin-thumb" loading="lazy">
                                          <?php else: ?>
                                              <span class="admin-thumb-placeholder"><i class="fa-regular fa-image"></i></span>
                                          <?php endif; ?>
                                      </td>
                                      <td>
                                          <a href="../clanki/<?= $articleId ?>" target="_blank" rel="noopener noreferrer" class="fw-semibold text-dark text-decoration-none">
                                              <?= escapeHtml((string) $row['naslov']) ?>
                                          </a>
                                          <?php if ($kraj !== ''): ?>
                                              <span class="small text-secondary ms-1">&middot; <?= escapeHtml($kraj) ?></span>
                                          <?php endif; ?>
                                          <?php if ($plainPreview !== ''): ?>
                                              <div class="admin-excerpt-cell mt-1"><?= escapeHtml($plainPreview) ?></div>
                                          <?php endif; ?>
                                      </td>
                                      <td>
                                          <span class="badge rounded-pill text-bg-light border text-secondary fw-medium">
                                              <?= escapeHtml($catLabel) ?>
                                          </span>
                                      </td>
                                      <td class="text-nowrap small text-secondary"><?= escapeHtml((string) $row['lepdatum']) ?></td>
                                      <td class="text-end text-nowrap">
                                          <a href="../clanki/<?= $articleId ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary" title="Ogled na strani">
                                              <i class="fa-solid fa-eye"></i>
                                          </a>
                                          <a class="btn btn-sm btn-outline-primary ms-1" href="uredi?id=<?= $articleId ?>">
                                              <i class="fa-solid fa-pen me-1"></i>Uredi
                                          </a>
                                          <form action="includes/brisi.inc.php" method="post" onsubmit="return confirm('Ali res želiš trajno izbrisati ta članek in njegove slike?');" class="d-inline ms-1">
                                              <input type="hidden" name="csrf_token" value="<?= escapeHtml($csrfToken) ?>">
                                              <input type="hidden" name="id" value="<?= $articleId ?>">
                                              <button type="submit" class="btn btn-sm btn-outline-danger" title="Izbriši članek">
                                                  <i class="fa-solid fa-trash-can"></i>
                                              </button>
                                          </form>
                                      </td>
                                  </tr>
                              <?php endforeach; ?>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </main>

      <script>
      (function () {
          var searchInput = document.getElementById('articleSearchInput');
          var catSelect = document.getElementById('articleCategoryFilter');
          var countEl = document.getElementById('visibleArticlesCount');
          var rows = Array.prototype.slice.call(document.querySelectorAll('#articlesTable tbody tr'));

          function applyFilter() {
              var q = (searchInput ? searchInput.value : '').toLowerCase().trim();
              var cat = catSelect ? catSelect.value : '';
              var shown = 0;

              rows.forEach(function (tr) {
                  var rowCat = tr.getAttribute('data-category') || '';
                  var text = tr.textContent.toLowerCase();
                  var matchCat = !cat || rowCat === cat;
                  var matchQuery = !q || text.indexOf(q) !== -1;
                  var visible = matchCat && matchQuery;
                  tr.style.display = visible ? '' : 'none';
                  if (visible) {
                      shown++;
                  }
              });

              if (countEl) {
                  countEl.textContent = String(shown);
              }
          }

          if (searchInput) {
              searchInput.addEventListener('input', applyFilter);
          }
          if (catSelect) {
              catSelect.addEventListener('change', applyFilter);
              if (catSelect.value) {
                  applyFilter();
              }
          }
      })();
      </script>
  </body>
</html>
