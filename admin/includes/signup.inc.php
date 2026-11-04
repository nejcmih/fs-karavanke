<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.inc.php';
requireAuth('../login');
requirePostRequest('../signup');
verifyCsrfToken(isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : null, '../signup?error=csrf');

$name = isset($_POST['name']) ? trim((string) $_POST['name']) : '';
$username = isset($_POST['uid']) ? trim((string) $_POST['uid']) : '';
$pwd = isset($_POST['pwd']) ? (string) $_POST['pwd'] : '';

require_once __DIR__ . '/dbh.inc.php';
require_once __DIR__ . '/functions.inc.php';

if (emptyInputSignup($name, $username, $pwd)) {
    header('Location: ../signup?error=emptyinput');
    exit();
}

if (invalidUid($username)) {
    header('Location: ../signup?error=invaliduid');
    exit();
}

if (uidExists($pdo, $username) !== false) {
    header('Location: ../signup?error=usernametaken');
    exit();
}

createUser($pdo, $name, $username, $pwd);
