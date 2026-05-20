<?php

return [
    'db' => [
        // Use environment variables in production (DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS, DB_CHARSET).
        // Keep local defaults empty to avoid exposing real credentials in source control.
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'name' => getenv('DB_NAME') ?: 'u755573950_ccruces_db',
        'user' => getenv('DB_USER') ?: 'u755573950_ccruces',
        'pass' => getenv('DB_PASS') ?: '#YhkWFDQvS4d6k5',
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    ],
];
