<?php
declare(strict_types=1);

require_once __DIR__ . '/admin/includes/image-thumb.inc.php';

$srcParam = isset($_GET['src']) ? (string) $_GET['src'] : '';
$wParam = filter_input(INPUT_GET, 'w', FILTER_VALIDATE_INT);
$requestedWidth = is_int($wParam) && $wParam > 0 ? $wParam : 960;

$resolved = resolveSafeMediaPath($srcParam);
if (!$resolved['ok']) {
    http_response_code(404);
    exit();
}

$target = getThumbCacheTarget($resolved, $requestedWidth);
$serveFile = $target['cacheAbs'];
$serveMime = $target['outMime'];

if (!is_file($serveFile)) {
    $created = generateThumbnailFile($resolved['srcAbs'], $target['cacheAbs'], $target['bucketWidth']);
    if (!$created || !is_file($target['cacheAbs'])) {
        // Graceful fallback to original image if GD cannot decode an edge-case format
        $serveFile = $resolved['srcAbs'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detected = $finfo !== false ? finfo_file($finfo, $serveFile) : false;
        $serveMime = is_string($detected) && str_starts_with($detected, 'image/') ? $detected : 'image/jpeg';
    }
}

$mtime = (int) (@filemtime($serveFile) ?: time());
$size = (int) (@filesize($serveFile) ?: 0);
$etag = '"' . sha1($serveFile . '|' . $mtime . '|' . $size) . '"';

header('Content-Type: ' . $serveMime);
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=31536000, immutable');
header('ETag: ' . $etag);

if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim((string) $_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
    http_response_code(304);
    exit();
}

if ($size > 0) {
    header('Content-Length: ' . $size);
}

readfile($serveFile);
exit();

