<?php

function users_all(): array
{
    $pdo = db();
    if ($pdo) {
        $stmt = $pdo->query('SELECT username, name, role, password_hash, status FROM users ORDER BY id ASC');
        return $stmt->fetchAll();
    }

    $rows = read_json_file('users.json', []);
    return is_array($rows) ? $rows : [];
}

function find_user(string $username): ?array
{
    $pdo = db();
    if ($pdo) {
        $stmt = $pdo->prepare('SELECT username, name, role, password_hash, status FROM users WHERE LOWER(username)=LOWER(?) LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    foreach (users_all() as $user) {
        if (isset($user['username']) && strcasecmp((string) $user['username'], $username) === 0) {
            return $user;
        }
    }

    return null;
}

function user_create(string $username, string $name, string $role, string $password): bool
{
    $username = trim($username);
    $name = trim($name);
    $role = in_array($role, ['admin', 'client'], true) ? $role : 'client';

    if ($username === '' || $name === '' || $password === '') {
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
