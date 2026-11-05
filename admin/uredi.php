<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.inc.php';
requireAuth('login');
require_once __DIR__ . '/includes/dbh.inc.php';
require_once __DIR__ . '/includes/functions.inc.php';
require_once __DIR__ . '/includes/sanitize-html.inc.php';
require_once __DIR__ . '/includes/image-thumb.inc.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!is_int($id) || $id <= 0) {
    header('Location: clanki');
    exit();
}

$stmt = $pdo->prepare('SELECT ID, naslov, kategorija, kraj, datum, besedilo, naslovnaslika, slike FROM clanki WHERE ID = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$article = $stmt->fetch();

if (!is_array($article)) {
    header('Location: clanki');
    exit();
}

$articleId = (int) $article['ID'];
$categories = getArticleCategories();
$currentCategory = trim((string) $article['kategorija']);
$csrfToken = getCsrfToken();
$error = isset($_GET['error']) ? (string) $_GET['error'] : '';

$editorInitialHtml = formatArticleBodyForDisplay((string) ($article['besedilo'] ?? ''));
$currentCover = trim((string) ($article['naslovnaslika'] ?? ''));
$slikeRaw = trim((string) ($article['slike'] ?? ''));
$existingGallery = $slikeRaw !== ''
    ? array_values(array_filter(explode('|', $slikeRaw), static fn(string $s): bool => trim($s) !== ''))
    : [];

$adminPageTitle = 'Uredi članek #' . $articleId;
$adminActive = 'clanki';
require __DIR__ . '/header.php';
?>
  <body class="d-flex flex-column min-vh-100">
      <?php require __DIR__ . '/meni.php'; ?>

      <main class="admin-main flex-grow-1">
          <div class="container">
              <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                  <div>
                      <h1 class="h3 fw-bold mb-1">Uredi članek <span class="text-secondary fw-normal">#<?= $articleId ?></span></h1>
                      <p class="text-secondary mb-0">Povzetek na seznamih novic se ob shranjevanju posodobi samodejno.</p>
                  </div>
                  <div class="d-flex gap-2">
                      <a href="../clanki/<?= $articleId ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary">
                          <i class="fa-solid fa-eye me-1"></i>Ogled članka
                      </a>
                      <a href="clanki" class="btn btn-outline-secondary">
                          <i class="fa-solid fa-arrow-left me-1"></i>Nazaj
                      </a>
                  </div>
              </div>

              <?php if ($error === 'emptyinput'): ?>
                  <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i>Izpolni obvezna polja (naslov, kategorija, datum)!</div>
              <?php elseif ($error === 'stmtfailed'): ?>
                  <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i>Napaka pri shranjevanju sprememb. Poskusi znova.</div>
              <?php elseif ($error === 'uploaderror'): ?>
                  <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i>Napaka pri nalaganju slik.</div>
              <?php elseif ($error === 'wrongtype'): ?>
                  <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i>Napačen format slike! Dovoljeni tipi so JPG, PNG in WebP.</div>
              <?php elseif ($error === 'filesize'): ?>
                  <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i>Velikost posamezne slike ne sme presegati 12 MB.</div>
              <?php elseif ($error === 'csrf'): ?>
                  <div class="alert alert-warning"><i class="fa-solid fa-triangle-exclamation me-2"></i>Seja je potekla ali pa je varnostni žeton neveljaven. Poskusi znova.</div>
              <?php endif; ?>

              <form id="articleEditorForm"
                    action="includes/uredi.inc.php"
                    method="post"
                    enctype="multipart/form-data"
                    data-csrf="<?= escapeHtml($csrfToken) ?>"
                    data-article-id="<?= $articleId ?>">
                  <input type="hidden" name="csrf_token" value="<?= escapeHtml($csrfToken) ?>">
                  <input type="hidden" name="id" value="<?= $articleId ?>">

                  <!-- Hidden file pickers for inline editor media -->
                  <input type="file" id="inlineSingleImageInput" class="d-none" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                  <input type="file" id="inlineGalleryInput" class="d-none" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" multiple>

                  <div class="row g-4">
                      <!-- Left Column: Title + Rich Content Editor -->
                      <div class="col-lg-8">
                          <div class="admin-card p-3 p-md-4">
                              <div class="mb-3">
                                  <label for="naslovInput" class="form-label fw-semibold">Naslov članka <span class="text-danger">*</span></label>
                                  <input type="text" id="naslovInput" name="naslov" class="form-control form-control-lg" maxlength="200" value="<?= escapeHtml((string) $article['naslov']) ?>" required>
                              </div>

                              <div>
                                  <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                                      <label class="form-label fw-semibold mb-0">Vsebina članka</label>
                                      <span class="small text-secondary"><i class="fa-regular fa-lightbulb me-1"></i>Slike lahko tudi povlečeš in spustiš med odstavke</span>
                                  </div>

                                  <div id="editorUploadStatus" class="d-none"></div>

                                  <div class="rich-editor-shell">
                                      <?php require __DIR__ . '/includes/editor-toolbar.inc.php'; ?>
                                      <div id="richEditor" class="rich-editor-surface" contenteditable="true" spellcheck="true"><?= $editorInitialHtml ?></div>
                                  </div>
                                  <textarea id="besediloInput" name="besedilo" class="d-none"></textarea>
                              </div>
                          </div>
                      </div>

                      <!-- Right Column: Metadata, Cover Image & Bottom Gallery -->
                      <div class="col-lg-4">
                          <div class="admin-card p-3 p-md-4 mb-4">
                              <h2 class="h6 fw-bold text-uppercase text-secondary mb-3">Podatki objave</h2>

                              <div class="mb-3">
                                  <label for="kategorijaSelect" class="form-label fw-medium small">Kategorija <span class="text-danger">*</span></label>
                                  <select id="kategorijaSelect" name="kategorija" class="form-select" required>
                                      <?php foreach ($categories as $value => $label): ?>
                                          <option value="<?= escapeHtml($value) ?>"<?= $value === $currentCategory ? ' selected' : '' ?>><?= escapeHtml($label) ?></option>
                                      <?php endforeach; ?>
                                  </select>
                              </div>

                              <div class="mb-3">
                                  <label for="datumInput" class="form-label fw-medium small">Datum dogodka / objave <span class="text-danger">*</span></label>
                                  <input type="date" id="datumInput" name="datum" class="form-control" value="<?= escapeHtml((string) $article['datum']) ?>" required>
                              </div>

                              <div class="mb-4">
                                  <label for="krajInput" class="form-label fw-medium small">Kraj (neobvezno)</label>
                                  <input type="text" id="krajInput" name="kraj" class="form-control" maxlength="100" value="<?= escapeHtml((string) ($article['kraj'] ?? '')) ?>">
                              </div>

                              <button type="submit" name="submit" class="btn btn-fsk w-100 py-2">
                                  <i class="fa-solid fa-floppy-disk me-2"></i>Shrani spremembe
                              </button>
                          </div>

                          <div class="admin-card p-3 p-md-4 mb-4">
                              <h2 class="h6 fw-bold text-uppercase text-secondary mb-2">Naslovna (predogledna) slika</h2>

                              <div id="coverPreviewWrap" class="cover-preview-box mb-3<?= $currentCover === '' ? ' d-none' : '' ?>">
                                  <img id="coverPreviewImg"
                                       src="<?= $currentCover !== '' ? escapeHtml(getThumbUrl('clanki/' . $articleId . '/' . $currentCover, 960, '../')) : '' ?>"
                                       alt="Naslovna slika">
                              </div>

                              <?php if ($currentCover !== ''): ?>
                                  <div class="form-check mb-2">
                                      <input class="form-check-input" type="checkbox" name="remove_cover" value="1" id="removeCoverCheck">
                                      <label class="form-check-label small text-danger" for="removeCoverCheck">Odstrani obstoječo naslovno sliko</label>
                                  </div>
                              <?php endif; ?>

                              <label for="coverFileInput" class="form-label small text-secondary mb-1">
                                  <?= $currentCover !== '' ? 'Zamenjaj naslovno sliko:' : 'Naloži naslovno sliko:' ?>
                              </label>
                              <input type="file" id="coverFileInput" name="previewfile" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                          </div>

                          <div class="admin-card p-3 p-md-4">
                              <h2 class="h6 fw-bold text-uppercase text-secondary mb-2">Galerija na koncu članka</h2>

                              <?php if ($existingGallery !== []): ?>
                                  <p class="small text-secondary mb-2">Označi slike, ki jih želiš odstraniti iz spodnje galerije:</p>
                                  <div class="existing-gallery-grid mb-3">
                                      <?php foreach ($existingGallery as $gImg): ?>
                                          <div class="existing-gallery-tile">
                                              <img src="<?= escapeHtml(getThumbUrl('clanki/' . $articleId . '/' . $gImg, 960, '../')) ?>" alt="" loading="lazy">
                                              <label>
                                                  <input type="checkbox" name="remove_slike[]" value="<?= escapeHtml($gImg) ?>">
                                                  <span>Briši</span>
                                              </label>
                                          </div>
                                      <?php endforeach; ?>
                                  </div>
                              <?php endif; ?>

                              <label class="form-label small text-secondary mb-1">Dodaj nove slike na konec članka:</label>
                              <input type="file" name="file[]" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" multiple>
                          </div>
                      </div>
                  </div>
              </form>
          </div>
      </main>

      <script src="../js/admin-editor.js?v=4" defer></script>
  </body>
</html>
