<?php

const DATA_DIR = __DIR__ . '/../../data';
const CONFIG_FILE = __DIR__ . '/../config.php';

function is_https_request(): bool
{
    if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
        return true;
    }
    if ((string) ($_SERVER['SERVER_PORT'] ?? '') === '443') {
        return true;
    }

    $forwardedProto = strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
    return $forwardedProto === 'https';
}

function start_secure_session(): void
{
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.cookie_secure', is_https_request() ? '1' : '0');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => is_https_request(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

function apply_security_headers(): void
{
    if (PHP_SAPI === 'cli' || headers_sent()) {
        return;
    }

    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self'; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'self'");

    if (is_https_request()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

start_secure_session();

function app_url(string $path = ''): string
{
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');
    $basePath = str_replace('\\', '/', dirname($scriptName));
    $basePath = rtrim($basePath, '/');
    if ($basePath === '/' || $basePath === '.' || $basePath === '\\') {
        $basePath = '';
    }

    return $basePath . '/' . ltrim($path, '/');
}

function e(string $value): string
{
    return htmlspecialchars(normalize_text($value), ENT_QUOTES, 'UTF-8');
}

function normalize_text(string $value): string
{
    // Repair common mojibake sequences caused by UTF-8 text decoded as Latin-1/Windows-1252.
    if ($value === '' || !preg_match('/[ÃÂð]/u', $value)) {
        return $value;
    }

    $converted = @iconv('UTF-8', 'Windows-1252//IGNORE', $value);
    if (is_string($converted) && $converted !== '' && mb_check_encoding($converted, 'UTF-8')) {
        return $converted;
    }

    $converted = @iconv('UTF-8', 'ISO-8859-1//IGNORE', $value);
    if (is_string($converted) && $converted !== '' && mb_check_encoding($converted, 'UTF-8')) {
        return $converted;
    }

    return $value;
}

function normalize_array_text(array $rows): array
{
    foreach ($rows as $key => $value) {
        if (is_array($value)) {
            $rows[$key] = normalize_array_text($value);
        } elseif (is_string($value)) {
            $rows[$key] = normalize_text($value);
        }
    }

    return $rows;
}

function app_config(): array
{
    static $config = null;
    if ($config !== null) {
        return $config;
    }

    $defaults = [
        'db' => [
            'host' => getenv('DB_HOST') ?: '',
            'port' => getenv('DB_PORT') ?: '3306',
            'name' => getenv('DB_NAME') ?: '',
            'user' => getenv('DB_USER') ?: '',
            'pass' => getenv('DB_PASS') ?: '',
            'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
        ],
    ];

    if (file_exists(CONFIG_FILE)) {
        $custom = require CONFIG_FILE;
        if (is_array($custom)) {
            $config = array_replace_recursive($defaults, $custom);
            return $config;
        }
    }

    $config = $defaults;
    return $config;
}

function db(): ?PDO
{
    static $pdo = null;
    static $attempted = false;

    if ($attempted) {
        return $pdo;
    }

    $attempted = true;
    $db = app_config()['db'] ?? [];

    if (($db['host'] ?? '') === '' || ($db['name'] ?? '') === '' || ($db['user'] ?? '') === '') {
        return null;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $db['host'],
        $db['port'] ?? '3306',
        $db['name'],
        $db['charset'] ?? 'utf8mb4'
    );

    try {
        $pdo = new PDO(
            $dsn,
            (string) $db['user'],
            (string) ($db['pass'] ?? ''),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        $pdo->exec('SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci');
    } catch (Throwable $e) {
        error_log('DB connection failed: ' . $e->getMessage());
        $pdo = null;
    }

    return $pdo;
}

function json_path(string $name): string
{
    return DATA_DIR . '/' . $name;
}

function read_json_file(string $name, mixed $fallback = []): mixed
{
    $path = json_path($name);
    if (!file_exists($path)) {
        return $fallback;
    }

    $raw = file_get_contents($path);
    if ($raw === false || $raw === '') {
        return $fallback;
    }

    if (strncmp($raw, "\xEF\xBB\xBF", 3) === 0) {
        $raw = substr($raw, 3);
    }

    $decoded = json_decode($raw, true);
    return is_null($decoded) ? $fallback : $decoded;
}

function write_json_file(string $name, mixed $data): bool
{
    $path = json_path($name);
    $encoded = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($encoded === false) {
        return false;
    }

    return file_put_contents($path, $encoded . PHP_EOL, LOCK_EX) !== false;
}
