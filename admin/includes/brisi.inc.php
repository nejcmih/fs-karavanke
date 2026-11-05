<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.inc.php';
requireAuth('../login');
requirePostRequest('../clanki');
verifyCsrfToken(isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : null, '../clanki?error=csrf');

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!is_int($id) || $id <= 0) {
    header('Location: ../clanki');
    exit();
}

require_once __DIR__ . '/dbh.inc.php';
require_once __DIR__ . '/image-thumb.inc.php';

try {
    $stmt = $pdo->prepare('DELETE FROM clanki WHERE ID = :id');
    $stmt->execute([':id' => $id]);
} catch (Throwable $e) {
    error_log('brisi.inc.php delete failed: ' . $e->getMessage());
    header('Location: ../clanki?error=stmtfailed');
    exit();
}

$articleDir = dirname(__DIR__, 2) . '/clanki/' . $id;
if (is_dir($articleDir)) {
    $entries = scandir($articleDir);
    if (is_array($entries)) {
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $entryPath = $articleDir . '/' . $entry;
            if (is_file($entryPath)) {
                deleteCachedThumbnails('clanki/' . $id . '/' . $entry);
                @unlink($entryPath);
            }
        }
    }
    @rmdir($articleDir);
}

header('Location: ../clanki?error=deleted');
exit();
