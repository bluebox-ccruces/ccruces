<?php

// Keep real production credentials out of Git.
$localConfig = __DIR__ . '/config.local.php';
if (file_exists($localConfig)) {
    $config = require $localConfig;
    if (is_array($config)) {
        return $config;
    }
}

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: '',
        'port' => getenv('DB_PORT') ?: '3306',
        'name' => getenv('DB_NAME') ?: '',
        'user' => getenv('DB_USER') ?: '',
        'pass' => getenv('DB_PASS') ?: '',
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    ],
];
