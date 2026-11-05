<?php
declare(strict_types=1);

require_once __DIR__ . '/admin/includes/dbh.inc.php';
require_once __DIR__ . '/admin/includes/sanitize-html.inc.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Ensure direct requests to /clanek.php?id=<ID> always redirect to /clanki/<ID>
if (is_int($id) && $id > 0 && isset($_SERVER['REQUEST_URI']) && preg_match('#/clanek(?:\.php)?(?:$|\?)#i', (string) $_SERVER['REQUEST_URI']) === 1) {
    $reqPath = (string) parse_url((string) $_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $baseDir = rtrim(str_replace('\\', '/', dirname($reqPath)), '/');
    header('Location: ' . $baseDir . '/clanki/' . $id, true, 301);
    exit();
}

$article = null;

if (is_int($id) && $id > 0) {
    $stmt = $pdo->prepare(
        'SELECT ID, naslov, kraj, DATE_FORMAT(datum, "%d. %m. %Y") AS lepdatum, besedilo, slike
         FROM clanki
         WHERE ID = :id
         LIMIT 1'
    );
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    if (is_array($row)) {
        $article = $row;
    }
}

if ($article === null) {
    http_response_code(404);
}

$articleId = $article !== null ? (int) $article['ID'] : 0;
$pageTitle = $article !== null
    ? (string) $article['naslov'] . ' | Folklorna skupina Karavanke'
    : 'Članek ni bil najden | Folklorna skupina Karavanke';
$basePrefix = '../';
$activeNav = '';

require __DIR__ . '/header.php';
?>
      <main class="domov wrapper flex-grow-1">
          <div class="container">
              <?php if ($article === null): ?>
                  <h1>Članek ni bil najden</h1>
                  <p>Zahtevani članek ne obstaja ali pa je bil odstranjen. <a href="../" class="povezava">Nazaj na domačo stran</a></p>
              <?php else: ?>
                  <?php
                  $naslovRaw = (string) $article['naslov'];
                  $naslovSafe = htmlspecialchars($naslovRaw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  $krajRaw = trim((string) ($article['kraj'] ?? ''));
                  $datumSafe = htmlspecialchars((string) $article['lepdatum'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  $datumLine = $krajRaw === ''
                      ? $datumSafe
                      : htmlspecialchars($krajRaw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . ', ' . $datumSafe;

                  $bodyHtml = formatArticleBodyForDisplay((string) $article['besedilo']);

                  $slikeRaw = trim((string) ($article['slike'] ?? ''));
                  $images = $slikeRaw !== ''
                      ? array_values(array_filter(explode('|', $slikeRaw), static fn(string $s): bool => trim($s) !== ''))
                      : [];
                  $thumbWidth = count($images) <= 2 ? 1280 : 960;
                  ?>
                  <h1><?= $naslovSafe ?></h1>
                  <div class="datum"><?= $datumLine ?></div>

                  <?php if ($bodyHtml !== ''): ?>
                      <div class="article-content">
                          <?= $bodyHtml ?>
                      </div>
                  <?php endif; ?>

                  <?php if ($images !== []): ?>
                      <div class="gallery">
                          <div class="row align-items-start">
                              <?php foreach ($images as $idx => $imgFile): ?>
                                  <?php if ($idx > 0 && $idx % 6 === 0): ?>
                                      </div><div class="row align-items-start">
                                  <?php endif; ?>
                                  <div class="col">
                                      <?= renderGalleryItem('clanki/' . $articleId . '/' . $imgFile, $naslovRaw, '../', $thumbWidth) ?>
                                  </div>
                              <?php endforeach; ?>
                          </div>
                      </div>
                  <?php endif; ?>
              <?php endif; ?>
          </div>
      </main>

      <?php require __DIR__ . '/footer.php'; ?>
  </body>
</html>
