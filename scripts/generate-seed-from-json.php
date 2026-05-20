<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Forbidden');
}

require_once __DIR__ . '/../includes/bootstrap.php';

function q(string $value): string
{
    return "'" . str_replace("'", "''", $value) . "'";
}

$users = read_json_file('users.json', []);
$services = read_json_file('services.json', []);
$posts = read_json_file('posts.json', []);

$lines = [];
$lines[] = "SET NAMES utf8mb4;";
$lines[] = "";

$lines[] = "INSERT INTO users (username, name, role, password_hash, status) VALUES";
$userRows = [];
foreach ($users as $user) {
    $userRows[] = sprintf(
        "(%s, %s, %s, %s, 1)",
        q((string) ($user['username'] ?? '')),
        q((string) ($user['name'] ?? '')),
        q((string) ($user['role'] ?? 'client')),
        q((string) ($user['password_hash'] ?? ''))
    );
}
$lines[] = implode(",\n", $userRows);
$lines[] = "ON DUPLICATE KEY UPDATE";
$lines[] = "name = VALUES(name),";
$lines[] = "role = VALUES(role),";
$lines[] = "password_hash = VALUES(password_hash),";
$lines[] = "status = VALUES(status);";
$lines[] = "";

$lines[] = "INSERT INTO services (id, name, tagline, description, logo, demo_url, private_url, status, sort_order) VALUES";
$serviceRows = [];
$sort = 1;
foreach ($services as $service) {
    $serviceRows[] = sprintf(
        "(%s, %s, %s, %s, %s, %s, %s, %s, %d)",
        q((string) ($service['id'] ?? '')),
        q((string) ($service['name'] ?? '')),
        q((string) ($service['tagline'] ?? '')),
        q((string) ($service['description'] ?? '')),
        q((string) ($service['logo'] ?? 'img/Icono BB.png')),
        q((string) ($service['demo_url'] ?? '')),
        q((string) ($service['private_url'] ?? '')),
        q((string) ($service['status'] ?? 'Disponible')),
        $sort
    );
    $sort++;
}
$lines[] = implode(",\n", $serviceRows);
$lines[] = "ON DUPLICATE KEY UPDATE";
$lines[] = "name = VALUES(name),";
$lines[] = "tagline = VALUES(tagline),";
$lines[] = "description = VALUES(description),";
$lines[] = "logo = VALUES(logo),";
$lines[] = "demo_url = VALUES(demo_url),";
$lines[] = "private_url = VALUES(private_url),";
$lines[] = "status = VALUES(status),";
$lines[] = "sort_order = VALUES(sort_order);";
$lines[] = "";

$lines[] = "INSERT INTO posts (id, title, excerpt, content, author, published_at) VALUES";
$postRows = [];
foreach ($posts as $post) {
    $postRows[] = sprintf(
        "(%s, %s, %s, %s, %s, %s)",
        q((string) ($post['id'] ?? '')),
        q((string) ($post['title'] ?? '')),
        q((string) ($post['excerpt'] ?? '')),
        q((string) ($post['content'] ?? '')),
        q((string) ($post['author'] ?? 'Carlos Cruces')),
        q((string) ($post['published_at'] ?? date('Y-m-d')))
    );
}
$lines[] = implode(",\n", $postRows);
$lines[] = "ON DUPLICATE KEY UPDATE";
$lines[] = "title = VALUES(title),";
$lines[] = "excerpt = VALUES(excerpt),";
$lines[] = "content = VALUES(content),";
$lines[] = "author = VALUES(author),";
$lines[] = "published_at = VALUES(published_at);";

$seedPath = __DIR__ . '/../database/seed.sql';
file_put_contents($seedPath, implode("\n", $lines) . "\n");

$post1 = null;
foreach ($posts as $p) {
    if (($p['id'] ?? '') === 'post-1') {
        $post1 = $p;
        break;
    }
}

if ($post1) {
    $updateSql = "SET NAMES utf8mb4;\n\n";
    $updateSql .= "UPDATE posts\nSET\n";
    $updateSql .= "  title = " . q((string) $post1['title']) . ",\n";
    $updateSql .= "  excerpt = " . q((string) $post1['excerpt']) . ",\n";
    $updateSql .= "  content = " . q((string) $post1['content']) . ",\n";
    $updateSql .= "  author = " . q((string) $post1['author']) . ",\n";
    $updateSql .= "  published_at = " . q((string) $post1['published_at']) . "\n";
    $updateSql .= "WHERE id = 'post-1';\n";

    file_put_contents(__DIR__ . '/../database/update-post-1.sql', $updateSql);
}

echo "SQL generated.\n";

