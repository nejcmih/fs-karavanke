<?php
declare(strict_types=1);

require_once __DIR__ . '/../admin/includes/dbh.inc.php';
require_once __DIR__ . '/../admin/includes/clanki-list.inc.php';

$pageTitle = 'Založništvo | Folklorna skupina Karavanke';
$basePrefix = '../';
$activeNav = 'o-nas';

require __DIR__ . '/../header.php';
?>
      <main class="domov wrapper flex-grow-1">
          <div class="domovVsebina container">
              <h1>Založništvo</h1>

              <?php
              renderArticleList($pdo, [
                  'category' => 'zaloznistvo',
                  'orderDir' => 'DESC',
                  'basePrefix' => '../',
                  'dateMode' => 'empty',
              ]);
              ?>
          </div>
      </main>

      <?php require __DIR__ . '/../footer.php'; ?>
  </body>
</html>
