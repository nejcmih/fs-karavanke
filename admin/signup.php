<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.inc.php';
requireAuth('login');

$csrfToken = getCsrfToken();

include_once __DIR__ . '/header.php';
?>
	<body class="d-flex flex-column min-vh-100">
		<?php include_once __DIR__ . '/meni.php'; ?>

		<main class="homepage wrapper flex-grow-1">
			<div class="container">	
                <section>
                    <h1>Registracija</h1>
                    <form action="includes/signup.inc.php" method="post">
                        <input type="hidden" name="csrf_token" value="<?= escapeHtml($csrfToken) ?>">
                        <table>
                            <tr>
                                <td>Ime:</td>
                                <td><input type="text" name="name" autocomplete="name" required></td>
                            </tr>
                            <tr>
                                <td>Uporabniško ime:</td>
                                <td><input type="text" name="uid" autocomplete="username" required></td>
                            </tr>
                            <tr>
                                <td>Geslo:</td>
                                <td><input type="password" name="pwd" autocomplete="new-password" required></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><button type="submit" name="submit" class="btn btn-outline-success my-3 btn-block"><i class="fa fa-sign-in px-2"></i>Registracija</button></td>
                            </tr>   
                        </table>
                    </form>
                    <?php
                        $error = isset($_GET['error']) ? (string) $_GET['error'] : '';
                        if ($error === 'emptyinput') {
                            echo '<p>Izpolni vsa polja!</p>';
                        } elseif ($error === 'invaliduid') {
                            echo '<p>Vnesi pravilno uporabniško ime!</p>';
                        } elseif ($error === 'stmtfailed') {
                            echo '<p>Napaka, poskusi znova!</p>';
                        } elseif ($error === 'usernametaken') {
                            echo '<p>Uporabniško ime že obstaja!</p>';
                        } elseif ($error === 'csrf') {
                            echo '<p>Seja je potekla ali pa je varnostni žeton neveljaven. Poskusi znova.</p>';
                        } elseif ($error === 'none') {
                            echo '<p>Registracija je uspela!</p>';
                        }
                    ?>  
                </section>
			</div>
		</main>
	</body>
</html>
