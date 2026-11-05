<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.inc.php';

$adminActive = isset($adminActive) && is_string($adminActive) ? $adminActive : '';
?>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top admin-navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= isAuthenticated() ? 'admin' : '../' ?>">
            <img src="../uploads/logo/logo-bel.png" alt="FS Karavanke logotip">
            <span class="d-none d-sm-inline fw-bold small text-uppercase tracking-wide opacity-90">Uredništvo</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbarCollapse" aria-controls="adminNavbarCollapse" aria-expanded="false" aria-label="Meni">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbarCollapse">
            <?php if (isAuthenticated()): ?>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link<?= $adminActive === 'dashboard' ? ' active' : '' ?>" href="admin">
                            <i class="fa-solid fa-gauge-high me-1"></i>Nadzorna plošča
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $adminActive === 'dodaj' ? ' active' : '' ?>" href="dodaj">
                            <i class="fa-solid fa-circle-plus me-1"></i>Nov članek
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $adminActive === 'clanki' ? ' active' : '' ?>" href="clanki">
                            <i class="fa-solid fa-newspaper me-1"></i>Vsi članki
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $adminActive === 'signup' ? ' active' : '' ?>" href="signup">
                            <i class="fa-solid fa-users me-1"></i>Uredniki
                        </a>
                    </li>
                </ul>

                <div class="d-flex flex-wrap align-items-center gap-2 mt-2 mt-lg-0">
                    <a href="../" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-light">
                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Spletna stran
                    </a>
                    <span class="admin-user-pill">
                        <i class="fa-regular fa-user me-1"></i><?= escapeHtml((string) ($_SESSION['useruid'] ?? '')) ?>
                    </span>
                    <form action="includes/logout.inc.php" method="post" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= escapeHtml(getCsrfToken()) ?>">
                        <button type="submit" class="btn btn-sm btn-light text-dark fw-medium">
                            <i class="fa-solid fa-right-from-bracket me-1"></i>Odjava
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../"><i class="fa-solid fa-house me-1"></i>Nazaj na spletno stran</a>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>
