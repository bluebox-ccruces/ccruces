<?php

require_once __DIR__ . '/core/app.php';
require_once __DIR__ . '/repositories/users_repository.php';
require_once __DIR__ . '/repositories/services_repository.php';
require_once __DIR__ . '/repositories/posts_repository.php';
require_once __DIR__ . '/repositories/post_images_repository.php';
require_once __DIR__ . '/repositories/post_interactions_repository.php';
require_once __DIR__ . '/service_modal.php';
require_once __DIR__ . '/core/session.php';

// Backward-compatible helpers.
function load_json(string $name, mixed $fallback = []): mixed
{
    if ($name === 'services.json') {
        return services_all();
    }
    if ($name === 'users.json') {
        return users_all();
    }
    if ($name === 'posts.json') {
        return posts_all();
    }

    return read_json_file($name, $fallback);
}

function save_json(string $name, mixed $data): bool
{
    return write_json_file($name, $data);
}
