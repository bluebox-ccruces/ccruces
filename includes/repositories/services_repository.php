<?php

function services_all(): array
{
    $pdo = db();
    if ($pdo) {
        $stmt = $pdo->query('SELECT id, name, tagline, description, logo, demo_url, private_url, status, sort_order FROM services ORDER BY sort_order ASC, name ASC');
        return $stmt->fetchAll();
    }

    $rows = read_json_file('services.json', []);
    return is_array($rows) ? $rows : [];
}

function service_by_id(string $id): ?array
{
    $pdo = db();
    if ($pdo) {
        $stmt = $pdo->prepare('SELECT id, name, tagline, description, logo, demo_url, private_url, status, sort_order FROM services WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $service = $stmt->fetch();
        return $service ?: null;
    }

    foreach (services_all() as $service) {
        if (($service['id'] ?? '') === $id) {
            return $service;
        }
    }

    return null;
}

function service_create(array $data): bool
{
    $id = trim((string) ($data['id'] ?? ''));
    $name = trim((string) ($data['name'] ?? ''));

    if ($id === '' || $name === '' || service_by_id($id)) {
        return false;
    }

    $payload = [
        'id' => $id,
        'name' => $name,
        'tagline' => trim((string) ($data['tagline'] ?? '')),
        'description' => trim((string) ($data['description'] ?? '')),
        'logo' => trim((string) ($data['logo'] ?? 'img/Icono BB.png')),
        'demo_url' => trim((string) ($data['demo_url'] ?? '')),
        'private_url' => trim((string) ($data['private_url'] ?? '')),
        'status' => trim((string) ($data['status'] ?? 'Activo')),
        'sort_order' => (int) ($data['sort_order'] ?? 0),
    ];

    $pdo = db();
    if ($pdo) {
        $stmt = $pdo->prepare('INSERT INTO services (id, name, tagline, description, logo, demo_url, private_url, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        return $stmt->execute([
            $payload['id'],
            $payload['name'],
            $payload['tagline'],
            $payload['description'],
            $payload['logo'],
            $payload['demo_url'],
            $payload['private_url'],
            $payload['status'],
            $payload['sort_order'],
        ]);
    }

    $rows = services_all();
    $rows[] = $payload;
    return write_json_file('services.json', $rows);
}

function service_update(string $id, array $data): bool
{
    $existing = service_by_id($id);
    if (!$existing) {
        return false;
    }

    $payload = [
        'name' => trim((string) ($data['name'] ?? ($existing['name'] ?? ''))),
        'tagline' => trim((string) ($data['tagline'] ?? ($existing['tagline'] ?? ''))),
        'description' => trim((string) ($data['description'] ?? ($existing['description'] ?? ''))),
        'logo' => trim((string) ($data['logo'] ?? ($existing['logo'] ?? 'img/Icono BB.png'))),
        'demo_url' => trim((string) ($data['demo_url'] ?? ($existing['demo_url'] ?? ''))),
        'private_url' => trim((string) ($data['private_url'] ?? ($existing['private_url'] ?? ''))),
        'status' => trim((string) ($data['status'] ?? ($existing['status'] ?? 'Activo'))),
        'sort_order' => (int) ($data['sort_order'] ?? ($existing['sort_order'] ?? 0)),
    ];

    if ($payload['name'] === '') {
        return false;
    }

    $pdo = db();
    if ($pdo) {
        $stmt = $pdo->prepare('UPDATE services SET name = ?, tagline = ?, description = ?, logo = ?, demo_url = ?, private_url = ?, status = ?, sort_order = ? WHERE id = ?');
        return $stmt->execute([
            $payload['name'],
            $payload['tagline'],
            $payload['description'],
            $payload['logo'],
            $payload['demo_url'],
            $payload['private_url'],
            $payload['status'],
            $payload['sort_order'],
            $id,
        ]);
    }

    $rows = services_all();
    foreach ($rows as &$row) {
        if (($row['id'] ?? '') === $id) {
            $row = array_merge($row, $payload);
            break;
        }
    }

    return write_json_file('services.json', $rows);
}

function service_delete(string $id): bool
{
    if ($id === '') {
        return false;
    }

    $pdo = db();
    if ($pdo) {
        $stmt = $pdo->prepare('DELETE FROM services WHERE id = ?');
        return $stmt->execute([$id]);
    }

    $rows = array_values(array_filter(services_all(), static fn(array $service): bool => ($service['id'] ?? '') !== $id));
    return write_json_file('services.json', $rows);
}
