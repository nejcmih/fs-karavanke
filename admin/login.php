<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.inc.php';
startSecureSession();

if (isAuthenticated()) {
    header('Location: admin');
    exit();
}

$csrfToken = getCsrfToken();
$error = isset($_GET['error']) ? (string) $_GET['error'] : '';

$adminPageTitle = 'Prijava';
require __DIR__ . '/header.php';
?>
  <body class="d-flex flex-column min-vh-100">
      <?php require __DIR__ . '/meni.php'; ?>

      <main class="admin-main flex-grow-1 d-flex align-items-center">
          <div class="container">
              <div class="auth-card admin-card p-4 p-sm-5 mx-auto">
                  <div class="text-center mb-4">
                      <div class="admin-stat-icon bg-primary bg-opacity-10 text-primary mx-auto mb-3">
                          <i class="fa-solid fa-lock"></i>
                      </div>
                      <h1 class="h4 fw-bold mb-1">Prijava v uredništvo</h1>
                      <p class="text-secondary small mb-0">KD Folklorna skupina Karavanke</p>
                  </div>

                  <?php if ($error === 'emptyinput'): ?>
                      <div class="alert alert-danger py-2 small">Izpolni uporabniško ime in geslo.</div>
                  <?php elseif ($error === 'wronglogin'): ?>
                      <div class="alert alert-danger py-2 small">Napačno uporabniško ime ali geslo.</div>
                  <?php elseif ($error === 'csrf'): ?>
                      <div class="alert alert-warning py-2 small">Seja je potekla. Poskusi se prijaviti znova.</div>
                  <?php endif; ?>

                  <form action="includes/login.inc.php" method="post">
                      <input type="hidden" name="csrf_token" value="<?= escapeHtml($csrfToken) ?>">

                      <div class="mb-3">
                          <label for="uidInput" class="form-label fw-medium small">Uporabniško ime</label>
                          <input type="text" id="uidInput" name="uid" class="form-control" autocomplete="username" required autofocus>
                      </div>

                      <div class="mb-4">
                          <label for="pwdInput" class="form-label fw-medium small">Geslo</label>
                          <input type="password" id="pwdInput" name="pwd" class="form-control" autocomplete="current-password" required>
                      </div>

                      <button type="submit" name="submit" class="btn btn-fsk w-100 py-2">
                          <i class="fa-solid fa-right-to-bracket me-2"></i>Prijava
                      </button>
                  </form>
              </div>
          </div>
      </main>
  </body>
</html>
