<?php
declare(strict_types=1);

require_once __DIR__ . '/../admin/includes/dbh.inc.php';
require_once __DIR__ . '/../admin/includes/clanki-list.inc.php';

$pageTitle = 'Ostalo | Folklorna skupina Karavanke';
$basePrefix = '../';
$activeNav = 'dogodki';

require __DIR__ . '/../header.php';
?>
      <main class="domov wrapper flex-grow-1">
          <div class="domovVsebina container">
              <h1>Ostalo</h1>

              <?php
              renderArticleList($pdo, [
                  'category' => 'ostalo',
                  'orderDir' => 'DESC',
                  'basePrefix' => '../',
              ]);
              ?>
          </div>
      </main>

      <?php require __DIR__ . '/../footer.php'; ?>
  </body>
</html>
