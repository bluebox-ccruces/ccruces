<?php

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function current_user(): ?array
{
    if (!isset($_SESSION['user'])) {
        return null;
    }

    $username = (string) $_SESSION['user'];
    $user = find_user($username);
    if (!$user) {
        unset($_SESSION['user']);
        return null;
    }

    if ((int) ($user['status'] ?? 1) !== 1) {
        unset($_SESSION['user']);
        return null;
    }

    return $user;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function is_admin(): bool
{
    $user = current_user();
    return $user !== null && (($user['role'] ?? '') === 'admin');
}

function require_login(): void
{
    if (is_logged_in()) {
        return;
    }

    set_flash('error', 'Debes iniciar sesión para continuar.');
    $currentScript = basename((string) ($_SERVER['SCRIPT_NAME'] ?? 'panel.php'));
    $allowed = ['panel.php', 'blog.php', 'servicios.php', 'index.php', 'admin.php', 'acceso.php'];
    $next = in_array($currentScript, $allowed, true) ? $currentScript : 'panel.php';
    header('Location: ' . app_url('login.php?next=' . urlencode($next)));
    exit;
}

function require_admin(): void
{
    require_login();
    if (!is_admin()) {
        set_flash('error', 'No tienes permisos para acceder al panel de administración.');
        header('Location: ' . app_url('panel.php'));
        exit;
    }
}

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}
