<?php

function user_accessible_services(string $username): array
{
    $username = trim($username);
    if ($username === '') {
        return [];
    }

    $pdo = db();
    if (!$pdo) {
        return services_all();
    }

    $stmt = $pdo->prepare(
        'SELECT DISTINCT
            s.id,
            s.name,
            s.tagline,
            s.description,
            s.summary,
            s.content,
            s.benefits,
            s.financial_benefits,
            s.roi_note,
            s.video_url,
            s.logo,
            s.demo_url,
            s.private_url,
            s.status,
            s.sort_order
         FROM users u
         INNER JOIN user_service_access usa ON usa.user_id = u.id
         INNER JOIN services s ON s.id = usa.service_id
         WHERE LOWER(u.username) = LOWER(?)
           AND u.company_id = ?
           AND usa.access_status = "active"
           AND s.company_id = ?
           AND s.is_active = 1
         ORDER BY s.sort_order ASC, s.name ASC'
    );
    $stmt->execute([$username, APP_COMPANY_ID, APP_COMPANY_ID]);
    return $stmt->fetchAll();
}

function user_has_service_access(string $username, string $serviceId): bool
{
    $username = trim($username);
    $serviceId = trim($serviceId);
    if ($username === '' || $serviceId === '') {
        return false;
    }

    $pdo = db();
    if (!$pdo) {
        return service_by_id($serviceId) !== null;
    }

    $stmt = $pdo->prepare(
        'SELECT 1
         FROM users u
         INNER JOIN user_service_access usa ON usa.user_id = u.id
         INNER JOIN services s ON s.id = usa.service_id
         WHERE LOWER(u.username) = LOWER(?)
           AND u.company_id = ?
           AND usa.service_id = ?
           AND usa.access_status = "active"
           AND s.company_id = ?
           AND s.is_active = 1
         LIMIT 1'
    );
    $stmt->execute([$username, APP_COMPANY_ID, $serviceId, APP_COMPANY_ID]);
    return (bool) $stmt->fetchColumn();
}

function user_roles(string $username, ?string $serviceId = null): array
{
    $username = trim($username);
    if ($username === '') {
        return [];
    }

    $pdo = db();
    if (!$pdo) {
        $user = find_user($username);
        return $user ? [(string) ($user['role'] ?? 'client')] : [];
    }

    $params = [$username, APP_COMPANY_ID];
    $serviceFilter = '';
    if ($serviceId !== null && $serviceId !== '') {
        $serviceFilter = ' AND (r.service_id = ? OR r.service_id IS NULL)';
        $params[] = $serviceId;
    }

    $stmt = $pdo->prepare(
        'SELECT r.code
         FROM users u
         INNER JOIN user_role_assignments ura ON ura.user_id = u.id
         INNER JOIN roles r ON r.id = ura.role_id
         WHERE LOWER(u.username) = LOWER(?)
           AND u.company_id = ?
           AND ura.assignment_status = "active"
           AND r.status = "active"' . $serviceFilter . '
         ORDER BY r.role_scope ASC, r.code ASC'
    );
    $stmt->execute($params);

    return array_map(static fn(array $row): string => (string) ($row['code'] ?? ''), $stmt->fetchAll());
}

function user_permissions(string $username, ?string $serviceId = null): array
{
    $username = trim($username);
    if ($username === '') {
        return [];
    }

    $pdo = db();
    if (!$pdo) {
        $user = find_user($username);
        return $user && (($user['role'] ?? '') === 'admin') ? ['*'] : [];
    }

    $params = [$username, APP_COMPANY_ID];
    $serviceFilter = '';
    if ($serviceId !== null && $serviceId !== '') {
        $serviceFilter = ' AND (p.service_id = ? OR p.service_id IS NULL)';
        $params[] = $serviceId;
    }

    $stmt = $pdo->prepare(
        'SELECT DISTINCT p.code
         FROM users u
         INNER JOIN user_role_assignments ura ON ura.user_id = u.id
         INNER JOIN roles r ON r.id = ura.role_id
         INNER JOIN role_permissions rp ON rp.role_id = r.id
         INNER JOIN permissions p ON p.id = rp.permission_id
         WHERE LOWER(u.username) = LOWER(?)
           AND u.company_id = ?
           AND ura.assignment_status = "active"
           AND r.status = "active"' . $serviceFilter . '
         ORDER BY p.code ASC'
    );
    $stmt->execute($params);

    return array_map(static fn(array $row): string => (string) ($row['code'] ?? ''), $stmt->fetchAll());
}

function user_has_permission(string $username, string $permissionCode, ?string $serviceId = null): bool
{
    $permissionCode = trim($permissionCode);
    if ($permissionCode === '') {
        return false;
    }

    $permissions = user_permissions($username, $serviceId);
    return in_array('*', $permissions, true) || in_array($permissionCode, $permissions, true);
}
