<?php

return [
    'db' => [
        // SECURITY: always prefer environment variables and do not commit real credentials.
        'host' => getenv('DB_HOST') ?: '',
        'port' => getenv('DB_PORT') ?: '3306',
        'name' => getenv('DB_NAME') ?: '',
        'user' => getenv('DB_USER') ?: '',
        'pass' => getenv('DB_PASS') ?: '',
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    ],
];
