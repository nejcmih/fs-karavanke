<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.inc.php';
requireAuth('login');
require_once __DIR__ . '/includes/dbh.inc.php';

$usersStmt = $pdo->query('SELECT usersId, usersName, usersUid FROM users ORDER BY usersId ASC');
$existingUsers = $usersStmt->fetchAll();

$csrfToken = getCsrfToken();
$error = isset($_GET['error']) ? (string) $_GET['error'] : '';

$adminPageTitle = 'Uredniki';
$adminActive = 'signup';
require __DIR__ . '/header.php';
?>
  <body class="d-flex flex-column min-vh-100">
      <?php require __DIR__ . '/meni.php'; ?>

      <main class="admin-main flex-grow-1">
          <div class="container">
              <div class="mb-4">
                  <h1 class="h3 fw-bold mb-1">Uredniki in avtorji</h1>
                  <p class="text-secondary mb-0">Upravljanje uporabniških računov za urejanje spletne strani</p>
              </div>

              <?php if ($error === 'emptyinput'): ?>
                  <div class="alert alert-danger">Izpolni vsa zahtevana polja!</div>
              <?php elseif ($error === 'invaliduid'): ?>
                  <div class="alert alert-danger">Uporabniško ime lahko vsebuje le črke (a-z) in številke (0-9).</div>
              <?php elseif ($error === 'weakpwd'): ?>
                  <div class="alert alert-danger">Geslo mora vsebovati vsaj 10 znakov.</div>
              <?php elseif ($error === 'usernametaken'): ?>
                  <div class="alert alert-danger">To uporabniško ime je že zasedeno.</div>
              <?php elseif ($error === 'stmtfailed'): ?>
                  <div class="alert alert-danger">Napaka pri shranjevanju, poskusi znova.</div>
              <?php elseif ($error === 'csrf'): ?>
                  <div class="alert alert-warning">Seja je potekla ali pa je varnostni žeton neveljaven.</div>
              <?php elseif ($error === 'none'): ?>
                  <div class="alert alert-success"><i class="fa-solid fa-circle-check me-2"></i>Nov uredniški račun je bil uspešno ustvarjen!</div>
              <?php endif; ?>

              <div class="row g-4">
                  <div class="col-lg-5">
                      <div class="admin-card p-4">
                          <h2 class="h5 fw-bold mb-3"><i class="fa-solid fa-user-plus me-2 text-primary"></i>Dodaj novega urednika</h2>
                          <form action="includes/signup.inc.php" method="post">
                              <input type="hidden" name="csrf_token" value="<?= escapeHtml($csrfToken) ?>">

                              <div class="mb-3">
                                  <label for="nameInput" class="form-label fw-medium small">Ime in priimek</label>
                                  <input type="text" id="nameInput" name="name" class="form-control" autocomplete="name" required>
                              </div>

                              <div class="mb-3">
                                  <label for="newUidInput" class="form-label fw-medium small">Uporabniško ime</label>
                                  <input type="text" id="newUidInput" name="uid" class="form-control" autocomplete="off" pattern="[A-Za-z0-9]+" title="Samo črke brez šumnikov in številke" required>
                              </div>

                              <div class="mb-4">
                                  <label for="newPwdInput" class="form-label fw-medium small">Geslo (vsaj 10 znakov)</label>
                                  <input type="password" id="newPwdInput" name="pwd" class="form-control" autocomplete="new-password" minlength="10" required>
                              </div>

                              <button type="submit" name="submit" class="btn btn-fsk w-100">
                                  <i class="fa-solid fa-check me-1"></i>Ustvari račun
                              </button>
                          </form>
                      </div>
                  </div>

                  <div class="col-lg-7">
                      <div class="admin-card p-4">
                          <h2 class="h5 fw-bold mb-3">Obstoječi računi (<?= count($existingUsers) ?>)</h2>
                          <div class="table-responsive">
                              <table class="table align-middle mb-0">
                                  <thead class="table-light">
                                      <tr>
                                          <th style="width: 60px;">ID</th>
                                          <th>Ime</th>
                                          <th>Uporabniško ime</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <?php foreach ($existingUsers as $u): ?>
                                          <tr>
                                              <td class="text-secondary small">#<?= (int) $u['usersId'] ?></td>
                                              <td class="fw-medium"><?= escapeHtml((string) $u['usersName']) ?></td>
                                              <td>
                                                  <span class="badge text-bg-light border text-dark font-monospace"><?= escapeHtml((string) $u['usersUid']) ?></span>
                                                  <?php if ((int) $u['usersId'] === (int) ($_SESSION['userid'] ?? 0)): ?>
                                                      <span class="badge bg-success bg-opacity-10 text-success ms-1">Ti</span>
                                                  <?php endif; ?>
                                              </td>
                                          </tr>
                                      <?php endforeach; ?>
                                  </tbody>
                              </table>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </main>
  </body>
</html>
