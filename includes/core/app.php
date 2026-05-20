<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const DATA_DIR = __DIR__ . '/../../data';
const CONFIG_FILE = __DIR__ . '/../config.php';

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
