<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.inc.php';
requireAuth('login');
require_once __DIR__ . '/includes/dbh.inc.php';

$stmt = $pdo->query('SELECT ID, naslov, kategorija, kraj, datum, preview, mapa FROM clanki ORDER BY ID DESC');
$rows = $stmt->fetchAll();
$csrfToken = getCsrfToken();

include_once __DIR__ . '/header.php';
?>
	<body class="d-flex flex-column min-vh-100">
		<?php include_once __DIR__ . '/meni.php'; ?>

		<main class="homepage wrapper flex-grow-1">
			<div class="container">	
                <section>
                    <?php
                        $error = isset($_GET['error']) ? (string) $_GET['error'] : '';
                        if ($error === 'deleted') {
                            echo '<p>Članek je izbrisan!</p>';
                        } elseif ($error === 'edited') {
                            echo '<p>Članek je bil uspešno urejen!</p>';
                        } elseif ($error === 'csrf') {
                            echo '<p>Seja je potekla ali pa je varnostni žeton neveljaven. Poskusi znova.</p>';
                        } elseif ($error === 'stmtfailed') {
                            echo '<p>Napaka pri izvedbi ukaza, poskusi znova!</p>';
                        }
                    ?>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr class="table-success">
                                <th>ID</th>
                                <th>Naslov</th>
                                <th>Kategorija</th>
                                <th>Kraj</th>
                                <th>Datum</th>
                                <th>Predogledno besedilo</th>
                                <th>Mapa</th>
                                <th>Uredi</th>
                                <th>Briši</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row): ?>
                                <?php $articleId = (int) $row['ID']; ?>
                                <tr>
                                    <td><?= $articleId ?></td>
                                    <td><a href="../clanki/<?= $articleId ?>" target="_blank" rel="noopener noreferrer"><?= escapeHtml((string) $row['naslov']) ?></a></td>
                                    <td><?= escapeHtml((string) $row['kategorija']) ?></td>
                                    <td><?= escapeHtml((string) ($row['kraj'] ?? '')) ?></td>
                                    <td><?= escapeHtml((string) $row['datum']) ?></td>
                                    <td><?= escapeHtml((string) ($row['preview'] ?? '')) ?></td>
                                    <td><?= escapeHtml((string) $row['mapa']) ?></td>
                                    <td><a class="btn btn-sm btn-outline-success" href="uredi?id=<?= $articleId ?>">Uredi</a></td>
                                    <td>
                                        <form action="includes/brisi.inc.php" method="post" onsubmit="return confirm('Ali želiš izbrisati članek?');" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= escapeHtml($csrfToken) ?>">
                                            <input type="hidden" name="id" value="<?= $articleId ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-warning">Briši</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
			</div>
		</main>
	</body>
</html>
