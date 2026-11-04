<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.inc.php';
requireAuth('login');
require_once __DIR__ . '/includes/functions.inc.php';

$categories = getArticleCategories();
$csrfToken = getCsrfToken();

include_once __DIR__ . '/header.php';
?>
	<body class="d-flex flex-column min-vh-100">
		<?php include_once __DIR__ . '/meni.php'; ?>

		<main class="homepage wrapper flex-grow-1">
			<div class="container">	
                <section>
                    <h1>Dodaj članek</h1>
                    <form action="includes/dodaj.inc.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= escapeHtml($csrfToken) ?>">
                        <table>
                            <tr>
                                <td>Naslov:</td>
                                <td><textarea name="naslov" rows="1" cols="130" required></textarea></td>
                            </tr>
                            <tr>
                                <td>Kategorija:</td>
                                <td>
                                    <select name="kategorija" size="1" required>
                                        <?php foreach ($categories as $value => $label): ?>
                                            <option value="<?= escapeHtml($value) ?>"><?= escapeHtml($label) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Kraj:</td>
                                <td><textarea name="kraj" rows="1" cols="80"></textarea></td>
                            </tr>
                            <tr>
                                <td>Datum:</td>
                                <td><input type="date" name="datum" required></td>
                            </tr>
                            <tr>
                                <td>Predogledno besedilo:</td>
                                <td><textarea name="preview" rows="5" cols="80" required></textarea></td>
                            </tr>
                            <tr>
                                <td>Besedilo:</td>
                                <td><textarea name="besedilo" rows="20" cols="130" required></textarea></td>
                            </tr>
                            <tr>
                                <td>Naloži predogledno sliko:</td>
                                <td><input type="file" name="previewfile" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"></td>
                            </tr>
                            <tr>
                                <td>Naloži slike:</td>
                                <td><input type="file" name="file[]" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" multiple></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><button type="submit" name="submit" class="btn btn-outline-success my-3 btn-block">Dodaj članek</button></td>
                            </tr>   
                        </table>
                    </form>
                    <?php
                        $error = isset($_GET['error']) ? (string) $_GET['error'] : '';
                        if ($error === 'emptyinput') {
                            echo '<p>Izpolni zahtevana polja!</p>';
                        } elseif ($error === 'stmtfailed') {
                            echo '<p>Napaka, poskusi znova!</p>';
                        } elseif ($error === 'uploaderror') {
                            echo '<p>Napaka pri nalaganju slik!</p>';
                        } elseif ($error === 'wrongtype') {
                            echo '<p>Napačen tip datoteke pri nalaganju! Dovoljeni tipi: jpg, jpeg, png, webp.</p>';
                        } elseif ($error === 'filesize') {
                            echo '<p>Velikost posamezne slike ne sme presegati 5 MB!</p>';
                        } elseif ($error === 'csrf') {
                            echo '<p>Seja je potekla ali pa je varnostni žeton neveljaven. Poskusi znova.</p>';
                        } elseif ($error === 'none') {
                            echo '<p>Članek je bil uspešno dodan!</p>';
                        }
                    ?>
                </section>
			</div>
		</main>
	</body>
</html>
