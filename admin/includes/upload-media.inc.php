<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.inc.php';
require_once __DIR__ . '/functions.inc.php';
require_once __DIR__ . '/image-thumb.inc.php';

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Seja je potekla. Prosim, prijavi se znova.']);
    exit();
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Neveljavna metoda zahteve.']);
    exit();
}

$submittedToken = isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : '';
$expectedToken = $_SESSION['csrf_token'] ?? '';
if (!is_string($expectedToken) || $expectedToken === '' || !hash_equals($expectedToken, $submittedToken)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Varnostni žeton (CSRF) je neveljaven. Osveži stran.']);
    exit();
}

$articleId = filter_input(INPUT_POST, 'article_id', FILTER_VALIDATE_INT);
$draftToken = isset($_POST['draft_token']) ? trim((string) $_POST['draft_token']) : '';

$clankiRoot = dirname(__DIR__, 2) . '/clanki';
cleanupStaleDraftDirs($clankiRoot);

if (is_int($articleId) && $articleId > 0) {
    $folderName = (string) $articleId;
} elseif (preg_match('/^[a-f0-9]{32}$/', $draftToken) === 1) {
    $folderName = '_draft_' . $draftToken;
} else {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Manjka identifikator članka ali osnutka.']);
    exit();
}

if (!isset($_FILES['files']['error'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Izberi vsaj eno sliko za nalaganje.']);
    exit();
}

$errors = (array) $_FILES['files']['error'];
$tmpNames = (array) ($_FILES['files']['tmp_name'] ?? []);
$sizes = (array) ($_FILES['files']['size'] ?? []);

$validatedBatch = [];
foreach ($errors as $idx => $errRaw) {
    $err = (int) $errRaw;
    if ($err === UPLOAD_ERR_NO_FILE) {
        continue;
    }

    $tmpPath = (string) ($tmpNames[$idx] ?? '');
    $size = (int) ($sizes[$idx] ?? 0);

    $validation = validateUploadedImage($tmpPath, $size, $err);
    if (!$validation['ok']) {
        $msg = match ($validation['error']) {
            'filesize' => 'Velikost posamezne slike ne sme presegati 12 MB.',
            'wrongtype' => 'Dovoljeni formati slik so JPG, PNG in WebP.',
            default => 'Napaka pri prenosu slike na strežnik.',
        };
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => $msg]);
        exit();
    }

    $validatedBatch[] = [
        'tmp_path' => $tmpPath,
        'filename' => $validation['filename'],
    ];
}

if ($validatedBatch === []) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Nobena veljavna slika ni bila izbrana.']);
    exit();
}

$targetDir = $clankiRoot . '/' . $folderName;
if (!is_dir($targetDir) && !@mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Ni mogoče ustvariti mape za slike članka.']);
    exit();
}

$uploadedItems = [];
foreach ($validatedBatch as $item) {
    $destPath = $targetDir . '/' . $item['filename'];
    if (!move_uploaded_file($item['tmp_path'], $destPath)) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Shranjevanje slike ni uspelo.']);
        exit();
    }
    @chmod($destPath, 0644);

    $relPath = 'clanki/' . $folderName . '/' . $item['filename'];
    prewarmThumbnails($relPath, [960, 1280]);

    $uploadedItems[] = [
        'filename' => $item['filename'],
        'fullUrl' => '../' . encodeRelPathForUrl($relPath),
        'thumb640Url' => getThumbUrl($relPath, 960, '../'),
        'thumb960Url' => getThumbUrl($relPath, 1280, '../'),
    ];
}

echo json_encode([
    'ok' => true,
    'items' => $uploadedItems,
]);
exit();

