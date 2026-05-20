<?php

const USER_MAX_FAILED_ATTEMPTS = 5;
const USER_LOCK_MINUTES = 15;

function users_all(): array
{
    $pdo = db();
    if ($pdo) {
        try {
            $stmt = $pdo->query('SELECT username, name, role, password_hash, status, last_login_at, failed_login_attempts, locked_until FROM users ORDER BY id ASC');
            return $stmt->fetchAll();
        } catch (Throwable) {
            // Backward compatibility for schemas without hardening columns yet.
            $stmt = $pdo->query('SELECT username, name, role, password_hash, status FROM users ORDER BY id ASC');
            return $stmt->fetchAll();
        }
    }

    $rows = read_json_file('users.json', []);
    return is_array($rows) ? $rows : [];
}

function find_user(string $username): ?array
{
    $pdo = db();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare('SELECT username, name, role, password_hash, status, last_login_at, failed_login_attempts, locked_until FROM users WHERE LOWER(username)=LOWER(?) LIMIT 1');
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            return $user ?: null;
        } catch (Throwable) {
            // Backward compatibility for schemas without hardening columns yet.
            $stmt = $pdo->prepare('SELECT username, name, role, password_hash, status FROM users WHERE LOWER(username)=LOWER(?) LIMIT 1');
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            return $user ?: null;
        }
    }

    foreach (users_all() as $user) {
        if (isset($user['username']) && strcasecmp((string) $user['username'], $username) === 0) {
            return $user;
        }
    }

    return null;
}

function user_username_is_valid(string $username): bool
{
    return (bool) preg_match('/^[a-zA-Z0-9._-]{3,64}$/', $username);
}

function user_password_policy_errors(string $password): array
{
    $errors = [];

    if (strlen($password) < 10) {
        $errors[] = 'La contraseña debe tener al menos 10 caracteres.';
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'La contraseña debe incluir al menos una letra minúscula.';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'La contraseña debe incluir al menos una letra mayúscula.';
    }
    if (!preg_match('/\d/', $password)) {
        $errors[] = 'La contraseña debe incluir al menos un número.';
    }
    if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
        $errors[] = 'La contraseña debe incluir al menos un carácter especial.';
    }

    return $errors;
}

function user_is_locked(array $user): bool
{
    $lockedUntil = (string) ($user['locked_until'] ?? '');
    if ($lockedUntil === '') {
        return false;
    }

    $untilTs = strtotime($lockedUntil);
    return $untilTs !== false && $untilTs > time();
}

function user_lock_remaining_seconds(array $user): int
{
    $lockedUntil = (string) ($user['locked_until'] ?? '');
    if ($lockedUntil === '') {
        return 0;
    }

    $untilTs = strtotime($lockedUntil);
    if ($untilTs === false) {
        return 0;
    }

    return max(0, $untilTs - time());
}

function user_record_successful_login(string $username): void
{
    $username = trim($username);
    if ($username === '') {
        return;
    }

    $pdo = db();
    if ($pdo) {
        $stmt = $pdo->prepare('UPDATE users SET failed_login_attempts = 0, locked_until = NULL, last_login_at = NOW() WHERE username = ?');
        $stmt->execute([$username]);
        return;
    }

    $rows = users_all();
    foreach ($rows as &$row) {
        if (strcasecmp((string) ($row['username'] ?? ''), $username) === 0) {
            $row['failed_login_attempts'] = 0;
            $row['locked_until'] = null;
            $row['last_login_at'] = date('c');
            break;
        }
    }

    write_json_file('users.json', $rows);
}

function user_record_failed_login(string $username): void
{
    $username = trim($username);
    if ($username === '') {
        return;
    }

    $user = find_user($username);
    if (!$user || user_is_locked($user)) {
        return;
    }

    $failedAttempts = (int) ($user['failed_login_attempts'] ?? 0) + 1;
    $lockedUntil = null;
    if ($failedAttempts >= USER_MAX_FAILED_ATTEMPTS) {
        $lockedUntil = date('Y-m-d H:i:s', time() + (USER_LOCK_MINUTES * 60));
        $failedAttempts = 0;
    }

    $pdo = db();
    if ($pdo) {
        if ($lockedUntil !== null) {
            $stmt = $pdo->prepare('UPDATE users SET failed_login_attempts = ?, locked_until = ? WHERE username = ?');
            $stmt->execute([$failedAttempts, $lockedUntil, $username]);
            return;
        }

        $stmt = $pdo->prepare('UPDATE users SET failed_login_attempts = ?, locked_until = NULL WHERE username = ?');
        $stmt->execute([$failedAttempts, $username]);
        return;
    }

    $rows = users_all();
    foreach ($rows as &$row) {
        if (strcasecmp((string) ($row['username'] ?? ''), $username) === 0) {
            $row['failed_login_attempts'] = $failedAttempts;
            $row['locked_until'] = $lockedUntil;
            break;
        }
    }

    write_json_file('users.json', $rows);
}

function user_create(string $username, string $name, string $role, string $password): bool
{
    $username = trim($username);
    $name = trim($name);
    $role = in_array($role, ['admin', 'client'], true) ? $role : 'client';

    if ($username === '' || $name === '' || $password === '') {
        return false;
    }

    if (!user_username_is_valid($username)) {
        return false;
    }

    if (!empty(user_password_policy_errors($password))) {
        return false;
    }

    if (find_user($username)) {
        return false;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $pdo = db();

    if ($pdo) {
        $stmt = $pdo->prepare('INSERT INTO users (username, name, role, password_hash, status) VALUES (?, ?, ?, ?, 1)');
        return $stmt->execute([$username, $name, $role, $hash]);
    }

    $rows = users_all();
    $rows[] = [
        'username' => $username,
        'name' => $name,
        'role' => $role,
        'password_hash' => $hash,
        'status' => 1,
        'last_login_at' => null,
        'failed_login_attempts' => 0,
        'locked_until' => null,
    ];

    return write_json_file('users.json', $rows);
}

function user_update(string $username, string $name, string $role, ?string $password = null, ?int $status = null): bool
{
    $existing = find_user($username);
    if (!$existing) {
        return false;
    }

    $name = trim($name);
    if ($name === '') {
        return false;
    }

    $role = in_array($role, ['admin', 'client'], true) ? $role : 'client';
    $statusValue = $status === null ? (int) ($existing['status'] ?? 1) : ($status === 0 ? 0 : 1);

    if ($password !== null && $password !== '' && !empty(user_password_policy_errors($password))) {
        return false;
    }

    $pdo = db();
    if ($pdo) {
        if ($password !== null && $password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET name = ?, role = ?, password_hash = ?, status = ? WHERE username = ?');
            return $stmt->execute([$name, $role, $hash, $statusValue, $username]);
        }

        $stmt = $pdo->prepare('UPDATE users SET name = ?, role = ?, status = ? WHERE username = ?');
        return $stmt->execute([$name, $role, $statusValue, $username]);
    }

    $rows = users_all();
    foreach ($rows as &$row) {
        if (strcasecmp((string) ($row['username'] ?? ''), $username) === 0) {
            $row['name'] = $name;
            $row['role'] = $role;
            $row['status'] = $statusValue;
            if ($password !== null && $password !== '') {
                $row['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            }
            break;
        }
    }

    return write_json_file('users.json', $rows);
}

function user_delete(string $username): bool
{
    if ($username === '') {
        return false;
    }

    $pdo = db();
    if ($pdo) {
        $stmt = $pdo->prepare('DELETE FROM users WHERE username = ?');
        return $stmt->execute([$username]);
    }

    $rows = array_values(array_filter(users_all(), static fn(array $user): bool => strcasecmp((string) ($user['username'] ?? ''), $username) !== 0));
    return write_json_file('users.json', $rows);
}
