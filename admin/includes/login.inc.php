<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.inc.php';
startSecureSession();
requirePostRequest('../login');
verifyCsrfToken(isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : null, '../login?error=csrf');

$username = isset($_POST['uid']) ? trim((string) $_POST['uid']) : '';
$pwd = isset($_POST['pwd']) ? (string) $_POST['pwd'] : '';

require_once __DIR__ . '/dbh.inc.php';
require_once __DIR__ . '/functions.inc.php';

if (emptyInputLogin($username, $pwd)) {
    header('Location: ../login?error=emptyinput');
    exit();
}

loginUser($pdo, $username, $pwd);
