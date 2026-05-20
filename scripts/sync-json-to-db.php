<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Forbidden');
}

require_once __DIR__ . '/../includes/bootstrap.php';

$pdo = db();
if (!$pdo) {
    fwrite(STDERR, "No DB connection. Update includes/config.php first.\n");
    exit(1);
}

$schema = file_get_contents(__DIR__ . '/../database/schema.sql');
if ($schema === false) {
    fwrite(STDERR, "Cannot read database/schema.sql\n");
    exit(1);
}

$pdo->exec($schema);

$users = read_json_file('users.json', []);
$services = read_json_file('services.json', []);
$posts = read_json_file('posts.json', []);

$pdo->beginTransaction();

try {
    $userStmt = $pdo->prepare(
        'INSERT INTO users (username, name, role, password_hash, status)
         VALUES (?, ?, ?, ?, 1)
         ON DUPLICATE KEY UPDATE name=VALUES(name), role=VALUES(role), password_hash=VALUES(password_hash), status=VALUES(status)'
    );

    foreach ($users as $user) {
        $userStmt->execute([
            $user['username'] ?? '',
            $user['name'] ?? '',
            $user['role'] ?? 'client',
            $user['password_hash'] ?? '',
        ]);
    }

    $svcStmt = $pdo->prepare(
        'INSERT INTO services (id, name, tagline, description, logo, demo_url, private_url, status, sort_order)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE name=VALUES(name), tagline=VALUES(tagline), description=VALUES(description), logo=VALUES(logo), demo_url=VALUES(demo_url), private_url=VALUES(private_url), status=VALUES(status), sort_order=VALUES(sort_order)'
    );

    $order = 1;
    foreach ($services as $service) {
        $svcStmt->execute([
            $service['id'] ?? '',
            $service['name'] ?? '',
            $service['tagline'] ?? '',
            $service['description'] ?? '',
            $service['logo'] ?? 'img/Icono BB.png',
            $service['demo_url'] ?? '',
            $service['private_url'] ?? '',
            $service['status'] ?? 'Disponible',
            $service['sort_order'] ?? $order,
        ]);
        $order++;
    }

    $postStmt = $pdo->prepare(
        'INSERT INTO posts (id, title, excerpt, content, author, published_at)
         VALUES (?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE title=VALUES(title), excerpt=VALUES(excerpt), content=VALUES(content), author=VALUES(author), published_at=VALUES(published_at)'
    );

    foreach ($posts as $post) {
        $postStmt->execute([
            $post['id'] ?? ('post-' . bin2hex(random_bytes(4))),
            $post['title'] ?? '',
            $post['excerpt'] ?? '',
            $post['content'] ?? '',
            $post['author'] ?? 'Carlos Cruces',
            $post['published_at'] ?? date('Y-m-d'),
        ]);
    }

    $pdo->commit();
    echo "Database sync completed successfully.\n";
} catch (Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, "Sync failed: " . $e->getMessage() . "\n");
    exit(1);
}

