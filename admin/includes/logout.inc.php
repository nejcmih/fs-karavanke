<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.inc.php';
startSecureSession();
requirePostRequest('../admin');
verifyCsrfToken(isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : null, '../admin');

destroySecureSession();

header('Location: ../../');
exit();
