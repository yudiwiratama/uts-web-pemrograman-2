<?php
// Konfigurasi database
return [
    'db_host' => getenv('DB_HOST') ?: 'mysql',
    'db_name' => getenv('DB_NAME') ?: 'db_inventory',
    'db_user' => getenv('DB_USERNAME') ?: 'root',
    'db_pass' => getenv('DB_PASSWORD') ?: 'unsia'
];
