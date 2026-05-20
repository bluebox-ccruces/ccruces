<?php

function post_likes_count(string $postId): int
{
    $postId = trim($postId);
    if ($postId === '') {
        return 0;
    }

    $pdo = db();
    if ($pdo) {
        $stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM post_likes WHERE post_id = ?');
        $stmt->execute([$postId]);
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }

    $likes = read_json_file('post_likes.json', []);
    if (!is_array($likes)) {
        return 0;
    }

    $count = 0;
    foreach ($likes as $like) {
        if ((string) ($like['post_id'] ?? '') === $postId) {
            $count++;
        }
    }

    return $count;
}

function post_is_liked_by_user(string $postId, string $username): bool
{
    $postId = trim($postId);
    $username = trim($username);
    if ($postId === '' || $username === '') {
        return false;
    }

    $pdo = db();
    if ($pdo) {
        $stmt = $pdo->prepare('SELECT id FROM post_likes WHERE post_id = ? AND username = ? LIMIT 1');
        $stmt->execute([$postId, $username]);
        return (bool) $stmt->fetch();
    }

    $likes = read_json_file('post_likes.json', []);
    if (!is_array($likes)) {
        return false;
    }

    foreach ($likes as $like) {
        if ((string) ($like['post_id'] ?? '') === $postId && strcasecmp((string) ($like['username'] ?? ''), $username) === 0) {
            return true;
        }
    }

    return false;
}

function post_toggle_like(string $postId, string $username): bool
{
    $postId = trim($postId);
    $username = trim($username);
    if ($postId === '' || $username === '') {
        return false;
    }

    $pdo = db();
    if ($pdo) {
        $check = $pdo->prepare('SELECT id FROM post_likes WHERE post_id = ? AND username = ? LIMIT 1');
        $check->execute([$postId, $username]);
        $row = $check->fetch();

        if ($row) {
            $del = $pdo->prepare('DELETE FROM post_likes WHERE id = ?');
            $del->execute([$row['id']]);
            return false;
        }

        $ins = $pdo->prepare('INSERT INTO post_likes (post_id, username) VALUES (?, ?)');
        $ins->execute([$postId, $username]);
        return true;
    }

    $likes = read_json_file('post_likes.json', []);
    if (!is_array($likes)) {
        $likes = [];
    }

    foreach ($likes as $index => $like) {
        if ((string) ($like['post_id'] ?? '') === $postId && strcasecmp((string) ($like['username'] ?? ''), $username) === 0) {
            unset($likes[$index]);
            write_json_file('post_likes.json', array_values($likes));
            return false;
        }
    }

    $likes[] = [
        'id' => 'like-' . bin2hex(random_bytes(8)),
        'post_id' => $postId,
        'username' => $username,
        'created_at' => date('c'),
    ];

    write_json_file('post_likes.json', $likes);
    return true;
}

function post_comments_for_post(string $postId): array
{
    $postId = trim($postId);
    if ($postId === '') {
        return [];
    }

    $pdo = db();
    if ($pdo) {
        $stmt = $pdo->prepare(
            'SELECT c.id, c.post_id, c.username, c.content, c.created_at, u.name AS user_name
             FROM post_comments c
             LEFT JOIN users u ON u.username = c.username
             WHERE c.post_id = ? AND c.status = "visible"
             ORDER BY c.created_at ASC, c.id ASC'
        );
        $stmt->execute([$postId]);
        return $stmt->fetchAll();
    }

    $comments = read_json_file('post_comments.json', []);
    if (!is_array($comments)) {
        return [];
    }

    $rows = array_values(array_filter($comments, static function (array $comment) use ($postId): bool {
        return (string) ($comment['post_id'] ?? '') === $postId && (string) ($comment['status'] ?? 'visible') === 'visible';
    }));

    usort($rows, static function (array $a, array $b): int {
        return strcmp((string) ($a['created_at'] ?? ''), (string) ($b['created_at'] ?? ''));
    });

    return $rows;
}

function post_comment_create(string $postId, string $username, string $content): bool
{
    $postId = trim($postId);
    $username = trim($username);
    $content = trim($content);

    if ($postId === '' || $username === '' || $content === '') {
        return false;
    }

    $pdo = db();
    if ($pdo) {
        $stmt = $pdo->prepare('INSERT INTO post_comments (post_id, username, content, status) VALUES (?, ?, ?, "visible")');
        return $stmt->execute([$postId, $username, $content]);
    }

    $comments = read_json_file('post_comments.json', []);
    if (!is_array($comments)) {
        $comments = [];
    }

    $user = find_user($username);
    $comments[] = [
        'id' => 'comment-' . bin2hex(random_bytes(8)),
        'post_id' => $postId,
        'username' => $username,
        'user_name' => (string) ($user['name'] ?? $username),
        'content' => $content,
        'status' => 'visible',
        'created_at' => date('c'),
    ];

    return write_json_file('post_comments.json', $comments);
}
