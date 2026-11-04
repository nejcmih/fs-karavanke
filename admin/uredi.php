<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.inc.php';
requireAuth('login');
require_once __DIR__ . '/includes/dbh.inc.php';
require_once __DIR__ . '/includes/functions.inc.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!is_int($id) || $id <= 0) {
    header('Location: clanki');
    exit();
}

$stmt = $pdo->prepare('SELECT ID, naslov, kategorija, kraj, datum, preview, besedilo FROM clanki WHERE ID = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$article = $stmt->fetch();

if (!is_array($article)) {
    header('Location: clanki');
    exit();
}

$categories = getArticleCategories();
$currentCategory = trim((string) $article['kategorija']);
$csrfToken = getCsrfToken();

include_once __DIR__ . '/header.php';
?>
	<body class="d-flex flex-column min-vh-100">
		<?php include_once __DIR__ . '/meni.php'; ?>

		<main class="homepage wrapper flex-grow-1">
			<div class="container">	
                <section>
                    <h1>Uredi članek</h1>
                    <form action="includes/uredi.inc.php" method="post">
                        <input type="hidden" name="csrf_token" value="<?= escapeHtml($csrfToken) ?>">
                        <input type="hidden" name="id" value="<?= (int) $article['ID'] ?>">
                        <table>
                            <tr>
                                <td>Naslov:</td>
                                <td><textarea name="naslov" rows="1" cols="130" required><?= escapeHtml((string) $article['naslov']) ?></textarea></td>
                            </tr>
                            <tr>
                                <td>Kategorija:</td>
                                <td>
                                    <select name="kategorija" size="1" required>
                                        <?php foreach ($categories as $value => $label): ?>
                                            <option value="<?= escapeHtml($value) ?>"<?= $value === $currentCategory ? ' selected' : '' ?>><?= escapeHtml($label) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Kraj:</td>
                                <td><textarea name="kraj" rows="1" cols="80"><?= escapeHtml((string) ($article['kraj'] ?? '')) ?></textarea></td>
                            </tr>
                            <tr>
                                <td>Datum:</td>
                                <td><input type="date" name="datum" value="<?= escapeHtml((string) $article['datum']) ?>" required></td>
                            </tr>
                            <tr>
                                <td>Predogledno besedilo:</td>
                                <td><textarea name="preview" rows="5" cols="80" required><?= escapeHtml((string) ($article['preview'] ?? '')) ?></textarea></td>
                            </tr>
                            <tr>
                                <td>Besedilo:</td>
                                <td><textarea name="besedilo" rows="20" cols="130" required><?= escapeHtml((string) ($article['besedilo'] ?? '')) ?></textarea></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><button type="submit" name="submit" class="btn btn-outline-success my-3 btn-block">Uredi članek</button></td>
                            </tr>   
                        </table>
                    </form>
                    <?php
                        $error = isset($_GET['error']) ? (string) $_GET['error'] : '';
                        if ($error === 'stmtfailed') {
                            echo '<p>Napaka, poskusi znova!</p>';
                        } elseif ($error === 'none') {
                            echo '<p>Članek je bil uspešno urejen!</p>';
                        } elseif ($error === 'emptyinput') {
                            echo '<p>Izpolni vsa polja!</p>';
                        } elseif ($error === 'csrf') {
                            echo '<p>Seja je potekla ali pa je varnostni žeton neveljaven. Poskusi znova.</p>';
                        }
                    ?>
                </section>
			</div>
		</main>
	</body>
</html>
