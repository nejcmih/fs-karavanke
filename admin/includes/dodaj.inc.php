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
$rawBesedilo = isset($_POST['besedilo']) ? (string) $_POST['besedilo'] : '';
$draftToken = isset($_POST['draft_token']) ? trim((string) $_POST['draft_token']) : '';

require_once __DIR__ . '/dbh.inc.php';
require_once __DIR__ . '/functions.inc.php';
require_once __DIR__ . '/sanitize-html.inc.php';
require_once __DIR__ . '/image-thumb.inc.php';

if (isInvalidArticleMeta($naslov, $kategorija, $datum)) {
    header('Location: ../dodaj?error=emptyinput');
    exit();
}

$cleanBesedilo = sanitizeArticleHtml($rawBesedilo);

// Validate optional cover image if provided
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

// Validate optional bottom-gallery images
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

if ($cleanBesedilo === '' && $coverTmpPath === '' && $galleryItems === []) {
    header('Location: ../dodaj?error=emptyinput');
    exit();
}

$autoPreview = generateArticleExcerpt($cleanBesedilo, 280);
$slikeColumn = implode('|', array_column($galleryItems, 'filename'));

$clankiRoot = dirname(__DIR__, 2) . '/clanki';
cleanupStaleDraftDirs($clankiRoot);

$draftDir = '';
if (preg_match('/^[a-f0-9]{32}$/', $draftToken) === 1) {
    $candidateDraft = $clankiRoot . '/_draft_' . $draftToken;
    if (is_dir($candidateDraft)) {
        $draftDir = $candidateDraft;
    }
}

$hasUploadedFiles = $coverTmpPath !== '' || $galleryItems !== [] || $draftDir !== '';
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
        ':preview' => $autoPreview,
        ':besedilo' => $cleanBesedilo,
        ':naslovnaslika' => $coverFilename,
        ':slike' => $slikeColumn,
        ':mapa' => '',
    ]);

    $newId = (int) $pdo->lastInsertId();
    if ($newId <= 0) {
        throw new RuntimeException('Failed to retrieve auto-increment ID for new article.');
    }

    if ($hasUploadedFiles) {
        $articleDir = $clankiRoot . '/' . $newId;
        if (!is_dir($articleDir) && !@mkdir($articleDir, 0755, true) && !is_dir($articleDir)) {
            throw new RuntimeException('Unable to create article media directory: ' . $articleDir);
        }

        // Move inline editor images from temporary _draft_<token> into permanent clanki/<newId>/
        if ($draftDir !== '' && is_dir($draftDir)) {
            $draftEntries = @scandir($draftDir) ?: [];
            $urlReplacements = [];

            foreach ($draftEntries as $entry) {
                if ($entry === '.' || $entry === '..' || !is_file($draftDir . '/' . $entry)) {
                    continue;
                }

                $draftRel = 'clanki/_draft_' . $draftToken . '/' . $entry;
                $finalRel = 'clanki/' . $newId . '/' . $entry;

                // Capture old draft thumbnail URLs & remove temporary draft thumbs
                $oldThumbUrls = [];
                $resolvedDraft = resolveSafeMediaPath($draftRel);
                if ($resolvedDraft['ok']) {
                    foreach (THUMB_ALLOWED_WIDTHS as $w) {
                        $oldThumbUrls[$w] = getThumbUrl($draftRel, $w, '../');
                        $draftTarget = getThumbCacheTarget($resolvedDraft, $w);
                        if (is_file($draftTarget['cacheAbs'])) {
                            @unlink($draftTarget['cacheAbs']);
                        }
                    }
                }

                $destInline = $articleDir . '/' . $entry;
                if (!@rename($draftDir . '/' . $entry, $destInline)) {
                    throw new RuntimeException('Failed to move staged inline image.');
                }
                @chmod($destInline, 0644);
                $movedFiles[] = $destInline;

                prewarmThumbnails($finalRel, [960, 1280]);
                foreach ($oldThumbUrls as $w => $oldUrl) {
                    $newUrl = getThumbUrl($finalRel, $w, '../');
                    if ($oldUrl !== $newUrl) {
                        $urlReplacements[$oldUrl] = $newUrl;
                    }
                }
            }

            @rmdir($draftDir);

            if ($urlReplacements !== []) {
                $cleanBesedilo = strtr($cleanBesedilo, $urlReplacements);
            }
            $cleanBesedilo = str_replace(
                'clanki/_draft_' . $draftToken . '/',
                'clanki/' . $newId . '/',
                $cleanBesedilo
            );
            $cleanBesedilo = sanitizeArticleHtml($cleanBesedilo);
        }

        if ($coverTmpPath !== '') {
            $coverDest = $articleDir . '/' . $coverFilename;
            if (!move_uploaded_file($coverTmpPath, $coverDest)) {
                throw new RuntimeException('Failed to move uploaded cover image.');
            }
            @chmod($coverDest, 0644);
            $movedFiles[] = $coverDest;
            prewarmThumbnails('clanki/' . $newId . '/' . $coverFilename, [960, 1280]);
        }

        foreach ($galleryItems as $item) {
            $dest = $articleDir . '/' . $item['filename'];
            if (!move_uploaded_file($item['tmp_path'], $dest)) {
                throw new RuntimeException('Failed to move uploaded gallery image.');
            }
            @chmod($dest, 0644);
            $movedFiles[] = $dest;
            prewarmThumbnails('clanki/' . $newId . '/' . $item['filename'], [960]);
        }
    }

    $updateStmt = $pdo->prepare('UPDATE clanki SET mapa = :mapa, besedilo = :besedilo WHERE ID = :id');
    $updateStmt->execute([
        ':mapa' => (string) $newId,
        ':besedilo' => $cleanBesedilo,
        ':id' => $newId,
    ]);

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

header('Location: ../clanki?error=added&id=' . $newId);
exit();
