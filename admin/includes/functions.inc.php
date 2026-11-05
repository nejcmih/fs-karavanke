<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.inc.php';

const MAX_IMAGE_UPLOAD_BYTES = 12582912; // 12 MB

/**
 * Allowed article categories in the database.
 *
 * @return array<string, string>
 */
function getArticleCategories(): array
{
    return [
        'koncerti' => 'Koncerti in prireditve',
        'nastopi' => 'Nastopi',
        'razstave' => 'Razstave',
        'delavnice' => 'Delavnice',
        'koledovanje' => 'Koledovanje',
        'ostalo' => 'Ostalo',
        'gostovanja' => 'Gostovanja',
        'napovednik' => 'Napovednik',
        'priznanja' => 'Dosežki in priznanja',
        'zabavno' => 'Zabavni kotiček',
        'zaloznistvo' => 'Založništvo',
        'extra' => '-Extra-',
    ];
}

function isValidCategory(string $kategorija): bool
{
    return array_key_exists($kategorija, getArticleCategories());
}

function isValidIsoDate(string $datum): bool
{
    $dt = DateTimeImmutable::createFromFormat('Y-m-d', $datum);
    return $dt !== false && $dt->format('Y-m-d') === $datum;
}

function emptyInputSignup(string $name, string $username, string $pwd): bool
{
    return trim($name) === '' || trim($username) === '' || $pwd === '';
}

function invalidUid(string $username): bool
{
    return preg_match('/^[a-zA-Z0-9]+$/', $username) !== 1;
}

/**
 * @return array{usersId: int|string, usersName: string, usersUid: string, usersPwd: string}|false
 */
function uidExists(PDO $pdo, string $username): array|false
{
    try {
        $stmt = $pdo->prepare('SELECT usersId, usersName, usersUid, usersPwd FROM users WHERE usersUid = :uid LIMIT 1');
        $stmt->execute([':uid' => $username]);
        $row = $stmt->fetch();
        return is_array($row) ? $row : false;
    } catch (Throwable $e) {
        error_log('uidExists query failed: ' . $e->getMessage());
        header('Location: ../signup?error=stmtfailed');
        exit();
    }
}

function createUser(PDO $pdo, string $name, string $username, string $pwd): void
{
    try {
        $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (usersName, usersUid, usersPwd) VALUES (:name, :uid, :pwd)');
        $stmt->execute([
            ':name' => trim($name),
            ':uid' => trim($username),
            ':pwd' => $hashedPwd,
        ]);
    } catch (Throwable $e) {
        error_log('createUser insert failed: ' . $e->getMessage());
        header('Location: ../signup?error=stmtfailed');
        exit();
    }

    header('Location: ../signup?error=none');
    exit();
}

function emptyInputLogin(string $username, string $pwd): bool
{
    return trim($username) === '' || $pwd === '';
}

function loginUser(PDO $pdo, string $username, string $pwd): void
{
    try {
        $stmt = $pdo->prepare('SELECT usersId, usersUid, usersPwd FROM users WHERE usersUid = :uid LIMIT 1');
        $stmt->execute([':uid' => trim($username)]);
        $user = $stmt->fetch();
    } catch (Throwable $e) {
        error_log('loginUser query failed: ' . $e->getMessage());
        header('Location: ../login?error=wronglogin');
        exit();
    }

    if (!is_array($user) || !password_verify($pwd, (string) $user['usersPwd'])) {
        header('Location: ../login?error=wronglogin');
        exit();
    }

    startSecureSession();
    session_regenerate_id(true);

    $_SESSION['userid'] = (int) $user['usersId'];
    $_SESSION['useruid'] = (string) $user['usersUid'];
    $_SESSION['last_activity'] = time();
    $_SESSION['last_regen'] = time();
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    header('Location: ../admin');
    exit();
}

/**
 * Validate required article metadata (title, valid category, valid ISO date).
 */
function isInvalidArticleMeta(string $naslov, string $kategorija, string $datum): bool
{
    $trimmedTitle = trim($naslov);
    return $trimmedTitle === ''
        || mb_strlen($trimmedTitle, 'UTF-8') > 200
        || !isValidCategory(trim($kategorija))
        || !isValidIsoDate(trim($datum));
}

/**
 * Validate an uploaded image using finfo_file() and generate a random safe filename.
 *
 * @return array{ok: true, filename: string}|array{ok: false, error: string}
 */
function validateUploadedImage(string $tmpPath, int $fileSize, int $fileError): array
{
    if ($fileError === UPLOAD_ERR_INI_SIZE || $fileError === UPLOAD_ERR_FORM_SIZE || $fileSize > MAX_IMAGE_UPLOAD_BYTES) {
        return ['ok' => false, 'error' => 'filesize'];
    }

    if ($fileError !== UPLOAD_ERR_OK || $fileSize <= 0 || $tmpPath === '' || !is_uploaded_file($tmpPath)) {
        return ['ok' => false, 'error' => 'uploaderror'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = $finfo !== false ? finfo_file($finfo, $tmpPath) : false;

    $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!is_string($mimeType) || !array_key_exists($mimeType, $allowedMimeTypes)) {
        return ['ok' => false, 'error' => 'wrongtype'];
    }

    $safeFilename = bin2hex(random_bytes(16)) . '.' . $allowedMimeTypes[$mimeType];

    return ['ok' => true, 'filename' => $safeFilename];
}

/**
 * Remove abandoned _draft_<hex> media folders older than $maxAgeSeconds.
 */
function cleanupStaleDraftDirs(string $clankiRoot, int $maxAgeSeconds = 86400): void
{
    if (!is_dir($clankiRoot)) {
        return;
    }

    $entries = @scandir($clankiRoot);
    if (!is_array($entries)) {
        return;
    }

    $now = time();
    foreach ($entries as $entry) {
        if (preg_match('/^_draft_[a-f0-9]{32}$/', $entry) !== 1) {
            continue;
        }
        $dirPath = $clankiRoot . '/' . $entry;
        if (!is_dir($dirPath)) {
            continue;
        }
        $mtime = (int) (@filemtime($dirPath) ?: 0);
        if ($mtime > 0 && ($now - $mtime) > $maxAgeSeconds) {
            $files = @scandir($dirPath);
            if (is_array($files)) {
                require_once __DIR__ . '/image-thumb.inc.php';
                foreach ($files as $f) {
                    if ($f !== '.' && $f !== '..' && is_file($dirPath . '/' . $f)) {
                        deleteCachedThumbnails('clanki/' . $entry . '/' . $f);
                        @unlink($dirPath . '/' . $f);
                    }
                }
            }
            @rmdir($dirPath);
        }
    }
}
