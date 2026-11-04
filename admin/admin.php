<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.inc.php';
requireAuth('login');

include_once __DIR__ . '/header.php';
?>
	<body class="d-flex flex-column min-vh-100">
		<?php include_once __DIR__ . '/meni.php'; ?>

		<main class="homepage wrapper flex-grow-1">
			<div class="container">	
                <section>
                    <h2><a href="dodaj">Dodaj članek</a></h2>
                    <br>
                    <h2><a href="clanki">Uredi/izbriši članek</a></h2>
                    <br>
                    <p>Prijavljen kot: <?= escapeHtml((string) $_SESSION['useruid']) ?></p>
                </section>
			</div>
		</main>
	</body>
</html>
