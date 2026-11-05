<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.inc.php';
requireAuth('login');
require_once __DIR__ . '/includes/functions.inc.php';

$categories = getArticleCategories();
$csrfToken = getCsrfToken();
$draftToken = bin2hex(random_bytes(16));
$error = isset($_GET['error']) ? (string) $_GET['error'] : '';

$adminPageTitle = 'Dodaj nov članek';
$adminActive = 'dodaj';
require __DIR__ . '/header.php';
?>
  <body class="d-flex flex-column min-vh-100">
      <?php require __DIR__ . '/meni.php'; ?>

      <main class="admin-main flex-grow-1">
          <div class="container">
              <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                  <div>
                      <h1 class="h3 fw-bold mb-1">Dodaj nov članek</h1>
                      <p class="text-secondary mb-0">Predogledni povzetek na seznamih novic se ustvari samodejno iz uvodnega besedila.</p>
                  </div>
                  <a href="clanki" class="btn btn-outline-secondary">
                      <i class="fa-solid fa-arrow-left me-1"></i>Nazaj na članke
                  </a>
              </div>

              <?php if ($error === 'emptyinput'): ?>
                  <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i>Vnesi naslov, kategorijo, datum ter dodaj vsebino ali slike!</div>
              <?php elseif ($error === 'stmtfailed'): ?>
                  <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i>Napaka pri shranjevanju članka. Poskusi znova.</div>
              <?php elseif ($error === 'uploaderror'): ?>
                  <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i>Napaka pri prenosu slik na strežnik.</div>
              <?php elseif ($error === 'wrongtype'): ?>
                  <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i>Napačen format slike! Dovoljeni tipi so JPG, PNG in WebP.</div>
              <?php elseif ($error === 'filesize'): ?>
                  <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i>Velikost posamezne slike ne sme presegati 12 MB.</div>
              <?php elseif ($error === 'csrf'): ?>
                  <div class="alert alert-warning"><i class="fa-solid fa-triangle-exclamation me-2"></i>Seja je potekla ali pa je varnostni žeton neveljaven. Poskusi znova.</div>
              <?php endif; ?>

              <form id="articleEditorForm"
                    action="includes/dodaj.inc.php"
                    method="post"
                    enctype="multipart/form-data"
                    data-csrf="<?= escapeHtml($csrfToken) ?>"
                    data-draft-token="<?= escapeHtml($draftToken) ?>">
                  <input type="hidden" name="csrf_token" value="<?= escapeHtml($csrfToken) ?>">
                  <input type="hidden" name="draft_token" value="<?= escapeHtml($draftToken) ?>">

                  <!-- Hidden file pickers for inline editor media -->
                  <input type="file" id="inlineSingleImageInput" class="d-none" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                  <input type="file" id="inlineGalleryInput" class="d-none" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" multiple>

                  <div class="row g-4">
                      <!-- Left Column: Title + Rich Content Editor -->
                      <div class="col-lg-8">
                          <div class="admin-card p-3 p-md-4">
                              <div class="mb-3">
                                  <label for="naslovInput" class="form-label fw-semibold">Naslov članka <span class="text-danger">*</span></label>
                                  <input type="text" id="naslovInput" name="naslov" class="form-control form-control-lg" maxlength="200" placeholder="Vnesi naslov objave…" required>
                              </div>

                              <div>
                                  <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                                      <label class="form-label fw-semibold mb-0">Vsebina članka</label>
                                      <span class="small text-secondary"><i class="fa-regular fa-lightbulb me-1"></i>Slike lahko tudi povlečeš in spustiš med odstavke</span>
                                  </div>

                                  <div id="editorUploadStatus" class="d-none"></div>

                                  <div class="rich-editor-shell">
                                      <?php require __DIR__ . '/includes/editor-toolbar.inc.php'; ?>
                                      <div id="richEditor" class="rich-editor-surface" contenteditable="true" spellcheck="true"></div>
                                  </div>
                                  <textarea id="besediloInput" name="besedilo" class="d-none"></textarea>
                              </div>
                          </div>
                      </div>

                      <!-- Right Column: Metadata, Cover Image & Optional Bottom Gallery -->
                      <div class="col-lg-4">
                          <div class="admin-card p-3 p-md-4 mb-4">
                              <h2 class="h6 fw-bold text-uppercase text-secondary mb-3">Podatki objave</h2>

                              <div class="mb-3">
                                  <label for="kategorijaSelect" class="form-label fw-medium small">Kategorija <span class="text-danger">*</span></label>
                                  <select id="kategorijaSelect" name="kategorija" class="form-select" required>
                                      <?php foreach ($categories as $value => $label): ?>
                                          <option value="<?= escapeHtml($value) ?>"><?= escapeHtml($label) ?></option>
                                      <?php endforeach; ?>
                                  </select>
                              </div>

                              <div class="mb-3">
                                  <label for="datumInput" class="form-label fw-medium small">Datum dogodka / objave <span class="text-danger">*</span></label>
                                  <input type="date" id="datumInput" name="datum" class="form-control" value="<?= escapeHtml(date('Y-m-d')) ?>" required>
                              </div>

                              <div class="mb-4">
                                  <label for="krajInput" class="form-label fw-medium small">Kraj (neobvezno)</label>
                                  <input type="text" id="krajInput" name="kraj" class="form-control" maxlength="100" placeholder="npr. Tržič">
                              </div>

                              <button type="submit" name="submit" class="btn btn-fsk w-100 py-2">
                                  <i class="fa-solid fa-paper-plane me-2"></i>Objavi članek
                              </button>
                          </div>

                          <div class="admin-card p-3 p-md-4 mb-4">
                              <h2 class="h6 fw-bold text-uppercase text-secondary mb-2">Naslovna (predogledna) slika</h2>
                              <p class="small text-secondary mb-3">Prikaže se ob povzetku članka na domači strani in v rubrikah.</p>

                              <div id="coverPreviewWrap" class="cover-preview-box mb-3 d-none">
                                  <img id="coverPreviewImg" src="" alt="Predogled naslovne slike">
                              </div>

                              <input type="file" id="coverFileInput" name="previewfile" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                          </div>

                          <div class="admin-card p-3 p-md-4">
                              <h2 class="h6 fw-bold text-uppercase text-secondary mb-2">Galerija na koncu članka</h2>
                              <p class="small text-secondary mb-3">Neobvezno — za galerijo čisto na dnu objave (med besedilo pa slike ali galerije vstavi z gumboma v orodni vrstici).</p>
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
