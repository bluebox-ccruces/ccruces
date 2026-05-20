<?php

function posts_all(string $query = ''): array
{
    $pdo = db();
    if ($pdo) {
        if ($query === '') {
            $stmt = $pdo->query('SELECT id, title, excerpt, content, author, published_at FROM posts ORDER BY published_at DESC, created_at DESC');
            return $stmt->fetchAll();
        }

        $like = '%' . $query . '%';
        $stmt = $pdo->prepare('SELECT id, title, excerpt, content, author, published_at FROM posts WHERE title LIKE ? OR excerpt LIKE ? OR content LIKE ? ORDER BY published_at DESC, created_at DESC');
        $stmt->execute([$like, $like, $like]);
        return $stmt->fetchAll();
    }

    $posts = read_json_file('posts.json', []);
    if (!is_array($posts)) {
        return [];
    }

    usort($posts, static fn(array $a, array $b): int => strcmp((string) ($b['published_at'] ?? ''), (string) ($a['published_at'] ?? '')));

    if ($query === '') {
        return $posts;
    }

    $toLower = static function (string $value): string {
        return function_exists('mb_strtolower') ? mb_strtolower($value) : strtolower($value);
    };

    return array_values(array_filter($posts, static function (array $post) use ($query, $toLower): bool {
        $haystack = $toLower((string) ($post['title'] ?? '') . ' ' . (string) ($post['excerpt'] ?? '') . ' ' . (string) ($post['content'] ?? ''));
        return strpos($haystack, $toLower($query)) !== false;
    }));
}

function post_by_id(string $id): ?array
{
    $pdo = db();
    if ($pdo) {
        $stmt = $pdo->prepare('SELECT id, title, excerpt, content, author, published_at FROM posts WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    foreach (posts_all() as $post) {
        if (($post['id'] ?? '') === $id) {
            return $post;
        }
    }

    return null;
}

function post_create(string $title, string $excerpt, string $content, string $author): bool
{
    $pdo = db();
    $postId = 'post-' . bin2hex(random_bytes(4));

    if ($pdo) {
        $stmt = $pdo->prepare('INSERT INTO posts (id, title, excerpt, content, author, published_at) VALUES (?, ?, ?, ?, ?, ?)');
        return $stmt->execute([$postId, $title, $excerpt, $content, $author, date('Y-m-d')]);
    }

    $posts = posts_all();
    $posts[] = [
        'id' => $postId,
        'title' => $title,
        'excerpt' => $excerpt,
        'content' => $content,
        'author' => $author,
        'published_at' => date('Y-m-d'),
    ];

    return write_json_file('posts.json', $posts);
}

function post_update(string $id, string $title, string $excerpt, string $content, string $author, string $publishedAt): bool
{
    if ($id === '' || $title === '' || $content === '' || $author === '') {
        return false;
    }

    $pdo = db();
    if ($pdo) {
        $stmt = $pdo->prepare('UPDATE posts SET title = ?, excerpt = ?, content = ?, author = ?, published_at = ? WHERE id = ?');
        return $stmt->execute([$title, $excerpt, $content, $author, $publishedAt, $id]);
    }

    $posts = posts_all();
    foreach ($posts as &$post) {
        if (($post['id'] ?? '') === $id) {
            $post['title'] = $title;
            $post['excerpt'] = $excerpt;
            $post['content'] = $content;
            $post['author'] = $author;
            $post['published_at'] = $publishedAt;
            break;
        }
    }

    return write_json_file('posts.json', $posts);
}

function post_delete(string $id): bool
{
    $pdo = db();
    if ($pdo) {
        $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
        return $stmt->execute([$id]);
    }

    $posts = array_values(array_filter(posts_all(), static fn(array $post): bool => ($post['id'] ?? '') !== $id));
    return write_json_file('posts.json', $posts);
}
