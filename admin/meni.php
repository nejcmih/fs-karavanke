<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.inc.php';
?>
<nav class="navbar navbar-expand-xl navbar-dark fixed-top bg-dark">
    <div class="container">
        <a class="navbar-brand" href="../"><img src="../uploads/logo/logo-bel.png" alt="FS Karavanke logotip" /></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>		
        </button>
		<div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
    			<li class="nav-item"><a class="nav-link active" aria-current="page" href="admin">Admin</a></li>
                <?php if (isAuthenticated()): ?>
                    <li class="nav-item"><a class="nav-link" href="signup">Dodaj novega uporabnika</a></li>
                    <li class="nav-item">
                        <form action="includes/logout.inc.php" method="post" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= escapeHtml(getCsrfToken()) ?>">
                            <button type="submit" class="nav-link btn btn-link text-uppercase border-0" style="margin: 0 20px 0 5px; text-decoration: none;">Odjava</button>
                        </form>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login">Prijava</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
