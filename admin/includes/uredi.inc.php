<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.inc.php';
requireAuth('../login');
requirePostRequest('../clanki');

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!is_int($id) || $id <= 0) {
    header('Location: ../clanki');
    exit();
}

verifyCsrfToken(
    isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : null,
    '../uredi?id=' . $id . '&error=csrf'
);

$naslov = isset($_POST['naslov']) ? trim((string) $_POST['naslov']) : '';
$kategorija = isset($_POST['kategorija']) ? trim((string) $_POST['kategorija']) : '';
$kraj = isset($_POST['kraj']) ? trim((string) $_POST['kraj']) : '';
$datum = isset($_POST['datum']) ? trim((string) $_POST['datum']) : '';
$preview = isset($_POST['preview']) ? trim((string) $_POST['preview']) : '';
$besedilo = isset($_POST['besedilo']) ? trim((string) $_POST['besedilo']) : '';

require_once __DIR__ . '/dbh.inc.php';
require_once __DIR__ . '/functions.inc.php';

if (emptyInputUredi($naslov, $kategorija, $datum, $preview, $besedilo)) {
    header('Location: ../uredi?id=' . $id . '&error=emptyinput');
    exit();
}

urediClanek($pdo, $id, $naslov, $kategorija, $kraj, $datum, $preview, $besedilo);
