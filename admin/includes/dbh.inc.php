<?php
declare(strict_types=1);

if (!function_exists('loadProjectEnv')) {
    function loadProjectEnv(string $envPath): void
    {
        if (!is_readable($envPath)) {
            return;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#') || !str_contains($trimmed, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $trimmed, 2);
            $key = trim($key);
            $value = trim($value);

            if (
                strlen($value) >= 2 &&
                (($value[0] === '"' && str_ends_with($value, '"')) || ($value[0] === "'" && str_ends_with($value, "'")))
            ) {
                $value = substr($value, 1, -1);
            }

            if ($key !== '' && !array_key_exists($key, $_ENV) && getenv($key) === false) {
                $_ENV[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }
}

// Load .env from project root (public_html/.env or local XAMPP folder)
// and fall back to one level above web root (/home/<cpanel_user>/.env on cPanel)
$projectRoot = dirname(__DIR__, 2);
loadProjectEnv($projectRoot . '/.env');
loadProjectEnv(dirname($projectRoot) . '/.env');

$serverName = (string) ($_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost');
$dBPort = (int) ($_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: 3306);
$dBName = (string) ($_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'shw28072_fskaravanke');
$dBUsername = (string) ($_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'root');
$dBPassword = (string) ($_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: '');
$dBCharset = (string) ($_ENV['DB_CHARSET'] ?? getenv('DB_CHARSET') ?: 'utf8mb4');

try {
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $serverName, $dBPort, $dBName, $dBCharset);
    $pdo = new PDO($dsn, $dBUsername, $dBPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Throwable $e) {
    error_log('Database connection error: ' . $e->getMessage());
    if (!headers_sent()) {
        http_response_code(500);
    }
    exit('Napaka pri povezavi s podatkovno bazo.');
}
