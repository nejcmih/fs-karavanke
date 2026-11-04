<?php
declare(strict_types=1);

const SESSION_IDLE_TIMEOUT = 7200; // 2 hours
const SESSION_REGEN_INTERVAL = 1800; // 30 minutes

/**
 * Start or resume a hardened PHP session before any output is sent.
 */
function startSecureSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        $isHttps = (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            ((int) ($_SERVER['SERVER_PORT'] ?? 80) === 443) ||
            (strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https')
        );

        if (!headers_sent()) {
            ini_set('session.use_strict_mode', '1');
            ini_set('session.use_only_cookies', '1');
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => $isHttps,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }

        session_start();
    }

    // Expire idle authenticated sessions server-side
    if (isset($_SESSION['userid'], $_SESSION['last_activity'])) {
        if ((time() - (int) $_SESSION['last_activity']) > SESSION_IDLE_TIMEOUT) {
            destroySecureSession();
            session_start();
            return;
        }
    }

    if (isset($_SESSION['userid'])) {
        $_SESSION['last_activity'] = time();

        if (!isset($_SESSION['last_regen'])) {
            $_SESSION['last_regen'] = time();
        } elseif ((time() - (int) $_SESSION['last_regen']) > SESSION_REGEN_INTERVAL && !headers_sent()) {
            session_regenerate_id(true);
            $_SESSION['last_regen'] = time();
        }
    }
}

/**
 * Destroy session data and expire the session cookie.
 */
function destroySecureSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION = [];

    if (ini_get('session.use_cookies') && !headers_sent()) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            [
                'expires' => time() - 42000,
                'path' => $params['path'] ?: '/',
                'domain' => $params['domain'] ?: '',
                'secure' => (bool) $params['secure'],
                'httponly' => (bool) $params['httponly'],
                'samesite' => $params['samesite'] ?: 'Lax',
            ]
        );
    }

    session_destroy();
}

/**
 * Check whether the current request belongs to an authenticated session.
 */
function isAuthenticated(): bool
{
    startSecureSession();

    return isset($_SESSION['userid'], $_SESSION['useruid'])
        && (int) $_SESSION['userid'] > 0
        && trim((string) $_SESSION['useruid']) !== '';
}

/**
 * Enforce server-side session authentication before rendering views or executing actions.
 */
function requireAuth(string $loginUrl = 'login'): void
{
    startSecureSession();

    if (!headers_sent()) {
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
    }

    if (!isAuthenticated()) {
        header('Location: ' . $loginUrl);
        exit();
    }
}

/**
 * Enforce HTTP POST method on state-changing endpoints.
 */
function requirePostRequest(string $redirectUrl): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        header('Location: ' . $redirectUrl);
        exit();
    }
}

/**
 * Return (and lazily generate) the per-session cryptographic CSRF token.
 */
function getCsrfToken(): string
{
    startSecureSession();

    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Validate a submitted CSRF token against the session token in constant time.
 */
function verifyCsrfToken(?string $submittedToken, string $redirectUrl): void
{
    startSecureSession();

    $expected = $_SESSION['csrf_token'] ?? '';
    if (
        !is_string($expected) ||
        $expected === '' ||
        !is_string($submittedToken) ||
        !hash_equals($expected, $submittedToken)
    ) {
        header('Location: ' . $redirectUrl);
        exit();
    }
}

/**
 * Escape dynamic strings safely for HTML contexts.
 */
function escapeHtml(?string $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

