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

require_once __DIR__ . '/dbh.inc.php';
require_once __DIR__ . '/functions.inc.php';
require_once __DIR__ . '/sanitize-html.inc.php';
require_once __DIR__ . '/image-thumb.inc.php';

$existingStmt = $pdo->prepare('SELECT ID, naslovnaslika, slike FROM clanki WHERE ID = :id LIMIT 1');
$existingStmt->execute([':id' => $id]);
$existing = $existingStmt->fetch();
if (!is_array($existing)) {
    header('Location: ../clanki');
    exit();
}

$naslov = isset($_POST['naslov']) ? trim((string) $_POST['naslov']) : '';
$kategorija = isset($_POST['kategorija']) ? trim((string) $_POST['kategorija']) : '';
$kraj = isset($_POST['kraj']) ? trim((string) $_POST['kraj']) : '';
$datum = isset($_POST['datum']) ? trim((string) $_POST['datum']) : '';
$rawBesedilo = isset($_POST['besedilo']) ? (string) $_POST['besedilo'] : '';

if (isInvalidArticleMeta($naslov, $kategorija, $datum)) {
    header('Location: ../uredi?id=' . $id . '&error=emptyinput');
    exit();
}

$cleanBesedilo = sanitizeArticleHtml($rawBesedilo);
$autoPreview = generateArticleExcerpt($cleanBesedilo, 280);

// Validate optional replacement cover image
$newCoverFilename = '';
$newCoverTmpPath = '';
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
        header('Location: ../uredi?id=' . $id . '&error=' . $coverValidation['error']);
        exit();
    }
    $newCoverFilename = $coverValidation['filename'];
    $newCoverTmpPath = (string) $_FILES['previewfile']['tmp_name'];
}

// Validate optional appended bottom-gallery images
$newGalleryItems = [];
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
            header('Location: ../uredi?id=' . $id . '&error=' . $validation['error']);
            exit();
        }

        $newGalleryItems[] = [
            'tmp_path' => $tmpPath,
            'filename' => $validation['filename'],
        ];
    }
}

$currentCover = trim((string) ($existing['naslovnaslika'] ?? ''));
$removeCover = (($_POST['remove_cover'] ?? '') === '1');

$slikeRaw = trim((string) ($existing['slike'] ?? ''));
$currentGallery = $slikeRaw !== ''
    ? array_values(array_filter(explode('|', $slikeRaw), static fn(string $s): bool => trim($s) !== ''))
    : [];

$removeSlike = [];
if (isset($_POST['remove_slike']) && is_array($_POST['remove_slike'])) {
    foreach ($_POST['remove_slike'] as $rmItem) {
        $rmStr = basename(trim((string) $rmItem));
        if ($rmStr !== '') {
            $removeSlike[$rmStr] = true;
        }
    }
}

$keptGallery = array_values(array_filter(
    $currentGallery,
    static fn(string $fname): bool => !isset($removeSlike[$fname])
));

$articleDir = dirname(__DIR__, 2) . '/clanki/' . $id;
if (($newCoverTmpPath !== '' || $newGalleryItems !== []) && !is_dir($articleDir)) {
    if (!@mkdir($articleDir, 0755, true) && !is_dir($articleDir)) {
        header('Location: ../uredi?id=' . $id . '&error=stmtfailed');
        exit();
    }
}

$finalCover = $currentCover;
if ($newCoverTmpPath !== '') {
    $destCover = $articleDir . '/' . $newCoverFilename;
    if (!move_uploaded_file($newCoverTmpPath, $destCover)) {
        header('Location: ../uredi?id=' . $id . '&error=uploaderror');
        exit();
    }
    @chmod($destCover, 0644);
    prewarmThumbnails('clanki/' . $id . '/' . $newCoverFilename, [960, 1280]);
    $finalCover = $newCoverFilename;
} elseif ($removeCover) {
    $finalCover = '';
}

foreach ($newGalleryItems as $item) {
    $dest = $articleDir . '/' . $item['filename'];
    if (!move_uploaded_file($item['tmp_path'], $dest)) {
        header('Location: ../uredi?id=' . $id . '&error=uploaderror');
        exit();
    }
    @chmod($dest, 0644);
    prewarmThumbnails('clanki/' . $id . '/' . $item['filename'], [960]);
    $keptGallery[] = $item['filename'];
}

$finalSlikeColumn = implode('|', $keptGallery);

try {
    $stmt = $pdo->prepare(
        'UPDATE clanki
         SET naslov = :naslov,
             kategorija = :kategorija,
             kraj = :kraj,
             datum = :datum,
             preview = :preview,
             besedilo = :besedilo,
             naslovnaslika = :naslovnaslika,
             slike = :slike
         WHERE ID = :id'
    );
    $stmt->execute([
        ':naslov' => $naslov,
        ':kategorija' => $kategorija,
        ':kraj' => $kraj,
        ':datum' => $datum,
        ':preview' => $autoPreview,
        ':besedilo' => $cleanBesedilo,
        ':naslovnaslika' => $finalCover,
        ':slike' => $finalSlikeColumn,
        ':id' => $id,
    ]);
} catch (Throwable $e) {
    error_log('uredi.inc.php update failed: ' . $e->getMessage());
    header('Location: ../uredi?id=' . $id . '&error=stmtfailed');
    exit();
}

// Clean up removed cover/gallery files from disk if no longer referenced in the article
$filesToConsiderRemoving = array_keys($removeSlike);
if (($removeCover || $newCoverTmpPath !== '') && $currentCover !== '' && $currentCover !== $finalCover) {
    $filesToConsiderRemoving[] = $currentCover;
}
foreach (array_unique($filesToConsiderRemoving) as $oldFile) {
    $safeName = basename($oldFile);
    if (
        $safeName === '' ||
        $safeName === $finalCover ||
        in_array($safeName, $keptGallery, true) ||
        str_contains($cleanBesedilo, rawurlencode($safeName)) ||
        str_contains($cleanBesedilo, $safeName)
    ) {
        continue;
    }
    $oldPath = $articleDir . '/' . $safeName;
    if (is_file($oldPath)) {
        deleteCachedThumbnails('clanki/' . $id . '/' . $safeName);
        @unlink($oldPath);
    }
}

header('Location: ../clanki?error=edited&id=' . $id);
exit();
