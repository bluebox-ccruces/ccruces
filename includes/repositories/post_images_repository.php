<?php

function post_image_path_normalize(string $imagePath): string
{
    $normalized = str_replace('\\', '/', trim($imagePath));
    if ($normalized === '') {
        return '';
    }

    if (preg_match('#^[a-z][a-z0-9+.-]*://#i', $normalized) === 1) {
        return $normalized;
    }

    $normalized = preg_replace('#^(?:\./)+#', '', $normalized) ?? $normalized;
    $normalized = ltrim($normalized, '/');
    if ($normalized === '') {
        return '';
    }

    if (str_starts_with($normalized, 'img/posts/')) {
        return $normalized;
    }

    if (str_starts_with($normalized, 'posts/')) {
        return 'img/' . $normalized;
    }

    if (!str_contains($normalized, '/')) {
        return 'img/posts/' . $normalized;
    }

    return $normalized;
}

function post_image_public_url(string $imagePath): string
{
    $normalized = post_image_path_normalize($imagePath);
    if ($normalized === '') {
        return '';
    }

    if (preg_match('#^[a-z][a-z0-9+.-]*://#i', $normalized) === 1) {
        return $normalized;
    }

    return app_url($normalized);
}

function post_images_normalize_rows(array $rows): array
{
    foreach ($rows as &$row) {
        $row['image_path'] = post_image_path_normalize((string) ($row['image_path'] ?? ''));
    }
    unset($row);

    return $rows;
}

function post_images_for_post(string $postId): array
{
    if ($postId === '') {
        return [];
    }

    $pdo = db();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare(
                'SELECT id, post_id, image_path, alt_text, is_primary, sort_order, created_at
                 FROM post_images
                 WHERE post_id = ?
                 ORDER BY is_primary DESC, sort_order ASC, id ASC'
            );
            $stmt->execute([$postId]);
            return post_images_normalize_rows($stmt->fetchAll());
        } catch (Throwable) {
            // Fall back to JSON when table is unavailable.
        }
    }

    $rows = read_json_file('post_images.json', []);
    if (!is_array($rows)) {
        return [];
    }

    $filtered = array_values(array_filter($rows, static fn(array $row): bool => (string) ($row['post_id'] ?? '') === $postId));
    $filtered = post_images_normalize_rows($filtered);
    usort($filtered, static function (array $a, array $b): int {
        $primaryCmp = ((int) ($b['is_primary'] ?? 0)) <=> ((int) ($a['is_primary'] ?? 0));
        if ($primaryCmp !== 0) {
            return $primaryCmp;
        }

        $sortCmp = ((int) ($a['sort_order'] ?? 0)) <=> ((int) ($b['sort_order'] ?? 0));
        if ($sortCmp !== 0) {
            return $sortCmp;
        }

        return strcmp((string) ($a['id'] ?? ''), (string) ($b['id'] ?? ''));
    });

    return $filtered;
}

function post_images_map_for_posts(array $postIds): array
{
    $postIds = array_values(array_filter(array_map(static fn($id): string => (string) $id, $postIds), static fn(string $id): bool => $id !== ''));
    if (empty($postIds)) {
        return [];
    }

    $map = [];
    foreach ($postIds as $postId) {
        $map[$postId] = [];
    }

    $pdo = db();
    if ($pdo) {
        try {
            $placeholders = implode(',', array_fill(0, count($postIds), '?'));
            $stmt = $pdo->prepare(
                "SELECT id, post_id, image_path, alt_text, is_primary, sort_order, created_at
                 FROM post_images
                 WHERE post_id IN ($placeholders)
                 ORDER BY post_id ASC, is_primary DESC, sort_order ASC, id ASC"
            );
            $stmt->execute($postIds);
            foreach ($stmt->fetchAll() as $row) {
                $postId = (string) ($row['post_id'] ?? '');
                if (!isset($map[$postId])) {
                    $map[$postId] = [];
                }
                $row['image_path'] = post_image_path_normalize((string) ($row['image_path'] ?? ''));
                $map[$postId][] = $row;
            }
            return $map;
        } catch (Throwable) {
            // Fall back to JSON when table is unavailable.
        }
    }

    $rows = read_json_file('post_images.json', []);
    if (!is_array($rows)) {
        return $map;
    }

    foreach ($rows as $row) {
        $postId = (string) ($row['post_id'] ?? '');
        if (isset($map[$postId])) {
            $row['image_path'] = post_image_path_normalize((string) ($row['image_path'] ?? ''));
            $map[$postId][] = $row;
        }
    }

    foreach ($map as $postId => $items) {
        usort($items, static function (array $a, array $b): int {
            $primaryCmp = ((int) ($b['is_primary'] ?? 0)) <=> ((int) ($a['is_primary'] ?? 0));
            if ($primaryCmp !== 0) {
                return $primaryCmp;
            }
            $sortCmp = ((int) ($a['sort_order'] ?? 0)) <=> ((int) ($b['sort_order'] ?? 0));
            if ($sortCmp !== 0) {
                return $sortCmp;
            }
            return strcmp((string) ($a['id'] ?? ''), (string) ($b['id'] ?? ''));
        });
        $map[$postId] = $items;
    }

    return $map;
}

function post_image_create(string $postId, string $imagePath, string $altText = '', int $sortOrder = 0, int $isPrimary = 0): bool
{
    $imagePath = post_image_path_normalize($imagePath);
    if ($postId === '' || $imagePath === '') {
        return false;
    }

    $pdo = db();
    if ($pdo) {
        try {
            if ($isPrimary === 1) {
                $stmtReset = $pdo->prepare('UPDATE post_images SET is_primary = 0 WHERE post_id = ?');
                $stmtReset->execute([$postId]);
            }

            $stmt = $pdo->prepare(
                'INSERT INTO post_images (post_id, image_path, alt_text, is_primary, sort_order)
                 VALUES (?, ?, ?, ?, ?)'
            );
            return $stmt->execute([$postId, $imagePath, $altText, $isPrimary, $sortOrder]);
        } catch (Throwable) {
            // Fall back to JSON when table is unavailable.
        }
    }

    $rows = read_json_file('post_images.json', []);
    if (!is_array($rows)) {
        $rows = [];
    }

    if ($isPrimary === 1) {
        foreach ($rows as &$row) {
            if ((string) ($row['post_id'] ?? '') === $postId) {
                $row['is_primary'] = 0;
            }
        }
        unset($row);
    }

    $rows[] = [
        'id' => 'img-' . bin2hex(random_bytes(6)),
        'post_id' => $postId,
        'image_path' => $imagePath,
        'alt_text' => $altText,
        'is_primary' => $isPrimary,
        'sort_order' => $sortOrder,
        'created_at' => date('Y-m-d H:i:s'),
    ];

    return write_json_file('post_images.json', $rows);
}

function post_image_set_primary(string $postId, string $imageId): bool
{
    if ($postId === '' || $imageId === '') {
        return false;
    }

    $pdo = db();
    if ($pdo) {
        try {
            $stmtReset = $pdo->prepare('UPDATE post_images SET is_primary = 0 WHERE post_id = ?');
            $stmtReset->execute([$postId]);
            $stmtPrimary = $pdo->prepare('UPDATE post_images SET is_primary = 1 WHERE id = ? AND post_id = ?');
            $stmtPrimary->execute([$imageId, $postId]);
            return $stmtPrimary->rowCount() > 0;
        } catch (Throwable) {
            // Fall back to JSON when table is unavailable.
        }
    }

    $rows = read_json_file('post_images.json', []);
    if (!is_array($rows)) {
        return false;
    }

    $found = false;
    foreach ($rows as &$row) {
        if ((string) ($row['post_id'] ?? '') === $postId) {
            if ((string) ($row['id'] ?? '') === $imageId) {
                $row['is_primary'] = 1;
                $found = true;
            } else {
                $row['is_primary'] = 0;
            }
        }
    }
    unset($row);

    return $found ? write_json_file('post_images.json', $rows) : false;
}

function post_image_delete(string $imageId, ?string &$deletedPath = null): bool
{
    if ($imageId === '') {
        return false;
    }

    $pdo = db();
    if ($pdo) {
        try {
            $stmtFind = $pdo->prepare('SELECT image_path FROM post_images WHERE id = ? LIMIT 1');
            $stmtFind->execute([$imageId]);
            $row = $stmtFind->fetch();
            if (!$row) {
                return false;
            }

            $deletedPath = (string) ($row['image_path'] ?? '');
            $stmtDelete = $pdo->prepare('DELETE FROM post_images WHERE id = ?');
            return $stmtDelete->execute([$imageId]);
        } catch (Throwable) {
            // Fall back to JSON when table is unavailable.
        }
    }

    $rows = read_json_file('post_images.json', []);
    if (!is_array($rows)) {
        return false;
    }

    $filtered = [];
    $found = false;
    foreach ($rows as $row) {
        if ((string) ($row['id'] ?? '') === $imageId) {
            $deletedPath = (string) ($row['image_path'] ?? '');
            $found = true;
            continue;
        }
        $filtered[] = $row;
    }

    return $found ? write_json_file('post_images.json', $filtered) : false;
}

function post_image_path_usage_count(string $imagePath): int
{
    if ($imagePath === '') {
        return 0;
    }

    $pdo = db();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare('SELECT COUNT(*) AS c FROM post_images WHERE image_path = ?');
            $stmt->execute([$imagePath]);
            $row = $stmt->fetch();
            return (int) ($row['c'] ?? 0);
        } catch (Throwable) {
            // Fall back to JSON when table is unavailable.
        }
    }

    $rows = read_json_file('post_images.json', []);
    if (!is_array($rows)) {
        return 0;
    }

    $count = 0;
    foreach ($rows as $row) {
        if ((string) ($row['image_path'] ?? '') === $imagePath) {
            $count++;
        }
    }

    return $count;
}

function post_images_delete_by_post_id(string $postId): array
{
    if ($postId === '') {
        return [];
    }

    $deletedPaths = [];
    $pdo = db();
    if ($pdo) {
        try {
            $stmtPaths = $pdo->prepare('SELECT image_path FROM post_images WHERE post_id = ?');
            $stmtPaths->execute([$postId]);
            foreach ($stmtPaths->fetchAll() as $row) {
                $path = (string) ($row['image_path'] ?? '');
                if ($path !== '') {
                    $deletedPaths[] = $path;
                }
            }

            $stmtDelete = $pdo->prepare('DELETE FROM post_images WHERE post_id = ?');
            $stmtDelete->execute([$postId]);
            return $deletedPaths;
        } catch (Throwable) {
            // Fall back to JSON when table is unavailable.
        }
    }

    $rows = read_json_file('post_images.json', []);
    if (!is_array($rows)) {
        return [];
    }

    $kept = [];
    foreach ($rows as $row) {
        if ((string) ($row['post_id'] ?? '') === $postId) {
            $path = (string) ($row['image_path'] ?? '');
            if ($path !== '') {
                $deletedPaths[] = $path;
            }
            continue;
        }
        $kept[] = $row;
    }

    write_json_file('post_images.json', $kept);
    return $deletedPaths;
}
