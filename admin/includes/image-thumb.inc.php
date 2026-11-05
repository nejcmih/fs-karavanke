<?php
declare(strict_types=1);

const THUMB_ALLOWED_WIDTHS = [960, 1280, 1600];
const THUMB_PASSTHROUGH_BYTES = 262144; // 256 KB

/**
 * Snap requested width to a fixed cache bucket to prevent disk exhaustion.
 */
function resolveThumbBucketWidth(int $requestedWidth): int
{
    foreach (THUMB_ALLOWED_WIDTHS as $bucket) {
        if ($requestedWidth <= $bucket) {
            return $bucket;
        }
    }

    return 1600;
}

/**
 * URL-encode each segment of a relative path while preserving forward slashes.
 */
function encodeRelPathForUrl(string $relPath): string
{
    $clean = ltrim(str_replace('\\', '/', $relPath), '/');
    $parts = explode('/', $clean);

    return implode('/', array_map('rawurlencode', $parts));
}

/**
 * Validate and resolve a relative media path strictly inside uploads/ or clanki/.
 *
 * @return array{ok: true, relPath: string, srcAbs: string, cacheDirAbs: string, cacheDirRel: string}|array{ok: false}
 */
function resolveSafeMediaPath(string $rawRelPath): array
{
    $rel = ltrim(trim(str_replace('\\', '/', $rawRelPath)), '/');
    if (
        $rel === '' ||
        str_contains($rel, "\0") ||
        str_contains($rel, '..') ||
        str_starts_with($rel, 'uploads/_thumbs/') ||
        str_starts_with($rel, 'clanki/_thumbs/')
    ) {
        return ['ok' => false];
    }

    $appRoot = dirname(__DIR__, 2);
    if (str_starts_with($rel, 'uploads/')) {
        $allowedRoot = realpath($appRoot . '/uploads');
        $cacheDirRel = 'uploads/_thumbs';
    } elseif (str_starts_with($rel, 'clanki/')) {
        $allowedRoot = realpath($appRoot . '/clanki');
        $cacheDirRel = 'clanki/_thumbs';
    } else {
        return ['ok' => false];
    }

    if ($allowedRoot === false) {
        return ['ok' => false];
    }

    $srcReal = realpath($appRoot . '/' . $rel);
    if ($srcReal === false || !is_file($srcReal)) {
        return ['ok' => false];
    }

    $allowedPrefix = rtrim(str_replace('\\', '/', $allowedRoot), '/') . '/';
    $srcNorm = str_replace('\\', '/', $srcReal);
    if (!str_starts_with($srcNorm, $allowedPrefix)) {
        return ['ok' => false];
    }

    $thumbsPrefix = $allowedPrefix . '_thumbs/';
    if (str_starts_with($srcNorm, $thumbsPrefix)) {
        return ['ok' => false];
    }

    return [
        'ok' => true,
        'relPath' => $rel,
        'srcAbs' => $srcReal,
        'cacheDirAbs' => $appRoot . '/' . $cacheDirRel,
        'cacheDirRel' => $cacheDirRel,
    ];
}

/**
 * Compute deterministic cached thumbnail filename and paths for a resolved media item.
 *
 * @param array{relPath: string, srcAbs: string, cacheDirAbs: string, cacheDirRel: string} $resolved
 * @return array{bucketWidth: int, cacheAbs: string, cacheRel: string, outMime: string}
 */
function getThumbCacheTarget(array $resolved, int $requestedWidth): array
{
    $bucketWidth = resolveThumbBucketWidth($requestedWidth);
    $ext = strtolower(pathinfo($resolved['srcAbs'], PATHINFO_EXTENSION));
    $usePng = ($ext === 'png');
    $outExt = $usePng ? 'png' : 'jpg';
    $outMime = $usePng ? 'image/png' : 'image/jpeg';

    $mtime = (int) (@filemtime($resolved['srcAbs']) ?: 0);
    $hash = substr(sha1('v2|' . $resolved['relPath'] . '|' . $mtime . '|w' . $bucketWidth), 0, 24);
    $filename = $hash . '_w' . $bucketWidth . '.' . $outExt;

    return [
        'bucketWidth' => $bucketWidth,
        'cacheAbs' => $resolved['cacheDirAbs'] . '/' . $filename,
        'cacheRel' => $resolved['cacheDirRel'] . '/' . $filename,
        'outMime' => $outMime,
    ];
}

/**
 * Return the fastest URL for an image thumbnail:
 * - Direct static _thumbs/ URL if already cached on disk
 * - Direct original URL if file is already small (<= 256 KB) or GD is unavailable
 * - On-demand thumb endpoint URL otherwise
 */
function getThumbUrl(string $relPath, int $maxWidth = 960, string $basePrefix = ''): string
{
    $fallbackUrl = $basePrefix . encodeRelPathForUrl($relPath);
    if (!extension_loaded('gd')) {
        return $fallbackUrl;
    }

    $resolved = resolveSafeMediaPath($relPath);
    if (!$resolved['ok']) {
        return $fallbackUrl;
    }

    $size = @filesize($resolved['srcAbs']);
    if (is_int($size) && $size > 0 && $size <= THUMB_PASSTHROUGH_BYTES) {
        return $fallbackUrl;
    }

    $target = getThumbCacheTarget($resolved, $maxWidth);
    if (is_file($target['cacheAbs'])) {
        return $basePrefix . $target['cacheRel'];
    }

    return $basePrefix . 'thumb?src=' . rawurlencode($resolved['relPath']) . '&w=' . $target['bucketWidth'];
}

/**
 * Generate and write a resized, EXIF-oriented, progressive thumbnail to disk.
 */
function generateThumbnailFile(string $srcAbs, string $destAbs, int $maxWidth, int $jpegQuality = 88): bool
{
    if (!extension_loaded('gd') || !is_file($srcAbs)) {
        return false;
    }

    $dir = dirname($destAbs);
    if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
        return false;
    }

    @ini_set('memory_limit', '256M');

    $info = @getimagesize($srcAbs);
    if (!is_array($info) || $info[0] <= 0 || $info[1] <= 0) {
        return false;
    }

    $mime = (string) ($info['mime'] ?? '');
    $srcImg = match ($mime) {
        'image/jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($srcAbs) : false,
        'image/png' => function_exists('imagecreatefrompng') ? @imagecreatefrompng($srcAbs) : false,
        'image/gif' => function_exists('imagecreatefromgif') ? @imagecreatefromgif($srcAbs) : false,
        'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($srcAbs) : false,
        default => false,
    };

    if (!$srcImg instanceof GdImage) {
        return false;
    }

    // Respect EXIF orientation on JPEGs from cameras/phones
    if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
        $exif = @exif_read_data($srcAbs);
        $orientation = is_array($exif) ? (int) ($exif['Orientation'] ?? 1) : 1;
        $rotated = match ($orientation) {
            3 => @imagerotate($srcImg, 180, 0),
            6 => @imagerotate($srcImg, -90, 0),
            8 => @imagerotate($srcImg, 90, 0),
            default => false,
        };
        if ($rotated instanceof GdImage) {
            imagedestroy($srcImg);
            $srcImg = $rotated;
        }
    }

    $origW = imagesx($srcImg);
    $origH = imagesy($srcImg);
    if ($origW <= 0 || $origH <= 0) {
        imagedestroy($srcImg);
        return false;
    }

    $targetW = min($origW, max(1, $maxWidth));
    $targetH = max(1, (int) round(($origH * $targetW) / $origW));

    $dstImg = imagecreatetruecolor($targetW, $targetH);
    if (!$dstImg instanceof GdImage) {
        imagedestroy($srcImg);
        return false;
    }

    $isPngOut = str_ends_with(strtolower($destAbs), '.png');
    if ($isPngOut) {
        imagealphablending($dstImg, false);
        imagesavealpha($dstImg, true);
        $transparent = imagecolorallocatealpha($dstImg, 0, 0, 0, 127);
        if ($transparent !== false) {
            imagefilledrectangle($dstImg, 0, 0, $targetW, $targetH, $transparent);
        }
    } else {
        $white = imagecolorallocate($dstImg, 255, 255, 255);
        if ($white !== false) {
            imagefilledrectangle($dstImg, 0, 0, $targetW, $targetH, $white);
        }
    }

    $resampled = imagecopyresampled(
        $dstImg,
        $srcImg,
        0,
        0,
        0,
        0,
        $targetW,
        $targetH,
        $origW,
        $origH
    );
    imagedestroy($srcImg);

    if (!$resampled) {
        imagedestroy($dstImg);
        return false;
    }

    $tmpDest = $destAbs . '.tmp.' . bin2hex(random_bytes(4));
    if ($isPngOut) {
        $saved = @imagepng($dstImg, $tmpDest, 7);
    } else {
        imageinterlace($dstImg, true);
        $saved = @imagejpeg($dstImg, $tmpDest, $jpegQuality);
    }
    imagedestroy($dstImg);

    if (!$saved || !is_file($tmpDest)) {
        @unlink($tmpDest);
        return false;
    }

    if (!@rename($tmpDest, $destAbs)) {
        @unlink($tmpDest);
        return is_file($destAbs);
    }

    @chmod($destAbs, 0644);
    return true;
}

/**
 * Eagerly generate standard thumbnail sizes when new images are uploaded in Admin.
 *
 * @param list<int> $widths
 */
function prewarmThumbnails(string $relPath, array $widths = [960, 1280]): void
{
    $resolved = resolveSafeMediaPath($relPath);
    if (!$resolved['ok']) {
        return;
    }

    $size = @filesize($resolved['srcAbs']);
    if (is_int($size) && $size > 0 && $size <= THUMB_PASSTHROUGH_BYTES) {
        return;
    }

    foreach ($widths as $w) {
        $target = getThumbCacheTarget($resolved, $w);
        if (!is_file($target['cacheAbs'])) {
            generateThumbnailFile($resolved['srcAbs'], $target['cacheAbs'], $target['bucketWidth']);
        }
    }
}

/**
 * Delete cached thumbnail files for a media file before deleting its original.
 */
function deleteCachedThumbnails(string $relPath): void
{
    $resolved = resolveSafeMediaPath($relPath);
    if (!$resolved['ok']) {
        return;
    }

    foreach (THUMB_ALLOWED_WIDTHS as $w) {
        $target = getThumbCacheTarget($resolved, $w);
        if (is_file($target['cacheAbs'])) {
            @unlink($target['cacheAbs']);
        }
    }
}

/**
 * Render a lightbox gallery item using a high-quality thumbnail in <img> and original in <a href>.
 */
function renderGalleryItem(
    string $relPath,
    string $alt = '',
    string $basePrefix = '',
    int $thumbWidth = 960
): string {
    $fullRaw = $basePrefix . encodeRelPathForUrl($relPath);
    $thumbRaw = getThumbUrl($relPath, $thumbWidth, $basePrefix);

    $fullUrl = htmlspecialchars($fullRaw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $thumbUrl = htmlspecialchars($thumbRaw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $altSafe = htmlspecialchars($alt, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $dataFullAttr = ($thumbRaw !== $fullRaw) ? ' data-full="' . $fullUrl . '"' : '';

    return '<a class="lightbox" href="' . $fullUrl . '">'
        . '<img src="' . $thumbUrl . '"' . $dataFullAttr . ' alt="' . $altSafe . '" loading="lazy" decoding="async">'
        . '</a>';
}

/**
 * Render an <img> that loads its fast thumbnail first and upgrades to full-res in the background.
 */
function renderProgressiveImg(
    string $relPath,
    string $alt = '',
    string $basePrefix = '',
    int $thumbWidth = 960,
    string $class = '',
    bool $eager = false
): string {
    $fullRaw = $basePrefix . encodeRelPathForUrl($relPath);
    $thumbRaw = getThumbUrl($relPath, $thumbWidth, $basePrefix);

    $fullSafe = htmlspecialchars($fullRaw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $thumbSafe = htmlspecialchars($thumbRaw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $altSafe = htmlspecialchars($alt, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $classAttr = $class !== ''
        ? ' class="' . htmlspecialchars($class, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"'
        : '';
    $dataFullAttr = ($thumbRaw !== $fullRaw) ? ' data-full="' . $fullSafe . '"' : '';
    $loadAttr = $eager ? ' fetchpriority="high" decoding="async"' : ' loading="lazy" decoding="async"';

    return '<img src="' . $thumbSafe . '"' . $dataFullAttr . ' alt="' . $altSafe . '"' . $classAttr . $loadAttr . '>';
}
