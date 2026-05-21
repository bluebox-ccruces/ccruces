<?php

return [
    'db' => [
        // SECURITY: always prefer environment variables and do not commit real credentials.
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'name' => getenv('DB_NAME') ?: 'u755573950_ccruces_db',
        'user' => getenv('DB_USER') ?: 'u755573950_ccruces',
        'pass' => getenv('DB_PASS') ?: '#YhkWFDQvS4d6k5',
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    ],
];
