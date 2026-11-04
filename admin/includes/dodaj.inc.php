<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.inc.php';
requireAuth('../login');
requirePostRequest('../dodaj');
verifyCsrfToken(isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : null, '../dodaj?error=csrf');

$naslov = isset($_POST['naslov']) ? trim((string) $_POST['naslov']) : '';
$kategorija = isset($_POST['kategorija']) ? trim((string) $_POST['kategorija']) : '';
$kraj = isset($_POST['kraj']) ? trim((string) $_POST['kraj']) : '';
$datum = isset($_POST['datum']) ? trim((string) $_POST['datum']) : '';
$preview = isset($_POST['preview']) ? trim((string) $_POST['preview']) : '';
$besedilo = isset($_POST['besedilo']) ? trim((string) $_POST['besedilo']) : '';

require_once __DIR__ . '/dbh.inc.php';
require_once __DIR__ . '/functions.inc.php';

if (emptyInputDodaj($naslov, $kategorija, $datum, $preview, $besedilo)) {
    header('Location: ../dodaj?error=emptyinput');
    exit();
}

// Validate optional preview/cover image if provided
$coverFilename = '';
$coverTmpPath = '';
if (
    isset($_FILES['previewfile']) &&
    (int) ($_FILES['previewfile']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE
) {
    $coverValidation = validateUploadedImage(
        (string) ($_FILES['previewfile']['tmp_name'] ?? ''),
        (int) ($_FILES['previewfile']['size'] ?? 0),
        (int) ($_FILES['previewfile']['error'] ?? UPLOAD_ERR_NO_FILE)
    );

    if (!$coverValidation['ok']) {
        header('Location: ../dodaj?error=' . $coverValidation['error']);
        exit();
    }

    $coverFilename = $coverValidation['filename'];
    $coverTmpPath = (string) $_FILES['previewfile']['tmp_name'];
}

// Validate optional gallery images (supports 0, 1, or 2+ images)
$galleryItems = [];
if (isset($_FILES['file']['error'])) {
    $errors = (array) $_FILES['file']['error'];
    $tmpNames = (array) ($_FILES['file']['tmp_name'] ?? []);
    $sizes = (array) ($_FILES['file']['size'] ?? []);

    foreach ($errors as $i => $errRaw) {
        $err = (int) $errRaw;
        if ($err === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        $tmpPath = (string) ($tmpNames[$i] ?? '');
        $size = (int) ($sizes[$i] ?? 0);

        $validation = validateUploadedImage($tmpPath, $size, $err);
        if (!$validation['ok']) {
            header('Location: ../dodaj?error=' . $validation['error']);
            exit();
        }

        $galleryItems[] = [
            'tmp_path' => $tmpPath,
            'filename' => $validation['filename'],
        ];
    }
}

$slikeColumn = implode('|', array_column($galleryItems, 'filename'));
$hasUploadedFiles = $coverTmpPath !== '' || $galleryItems !== [];
$articleDir = '';
$movedFiles = [];

try {
    $pdo->beginTransaction();

    $insertStmt = $pdo->prepare(
        'INSERT INTO clanki (naslov, kategorija, kraj, datum, preview, besedilo, naslovnaslika, slike, mapa)
         VALUES (:naslov, :kategorija, :kraj, :datum, :preview, :besedilo, :naslovnaslika, :slike, :mapa)'
    );
    $insertStmt->execute([
        ':naslov' => $naslov,
        ':kategorija' => $kategorija,
        ':kraj' => $kraj,
        ':datum' => $datum,
        ':preview' => $preview,
        ':besedilo' => $besedilo,
        ':naslovnaslika' => $coverFilename,
        ':slike' => $slikeColumn,
        ':mapa' => '',
    ]);

    $newId = (int) $pdo->lastInsertId();
    if ($newId <= 0) {
        throw new RuntimeException('Failed to retrieve auto-increment ID for new article.');
    }

    $updateMapaStmt = $pdo->prepare('UPDATE clanki SET mapa = :mapa WHERE ID = :id');
    $updateMapaStmt->execute([
        ':mapa' => (string) $newId,
        ':id' => $newId,
    ]);

    if ($hasUploadedFiles) {
        $articleDir = dirname(__DIR__, 2) . '/clanki/' . $newId;
        if (!is_dir($articleDir) && !@mkdir($articleDir, 0755, true) && !is_dir($articleDir)) {
            throw new RuntimeException('Unable to create article media directory: ' . $articleDir);
        }

        if ($coverTmpPath !== '') {
            $coverDest = $articleDir . '/' . $coverFilename;
            if (!move_uploaded_file($coverTmpPath, $coverDest)) {
                throw new RuntimeException('Failed to move uploaded cover image.');
            }
            $movedFiles[] = $coverDest;
        }

        foreach ($galleryItems as $item) {
            $dest = $articleDir . '/' . $item['filename'];
            if (!move_uploaded_file($item['tmp_path'], $dest)) {
                throw new RuntimeException('Failed to move uploaded gallery image.');
            }
            $movedFiles[] = $dest;
        }
    }

    $pdo->commit();
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    foreach ($movedFiles as $filePath) {
        if (is_file($filePath)) {
            @unlink($filePath);
        }
    }
    if ($articleDir !== '' && is_dir($articleDir)) {
        @rmdir($articleDir);
    }

    error_log('dodaj.inc.php failed: ' . $e->getMessage());
    header('Location: ../dodaj?error=stmtfailed');
    exit();
}

header('Location: ../dodaj?error=none');
exit();
