<?php
declare(strict_types=1);

function read_env(): array
{
    $path = __DIR__ . '/.env';
    if (!is_readable($path)) {
        throw new RuntimeException('Chybí konfigurace databáze v config/.env.');
    }

    $config = parse_ini_file($path);
    foreach (['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD'] as $key) {
        if ($config === false || !array_key_exists($key, $config)) {
            throw new RuntimeException('Neúplná konfigurace databáze.');
        }
    }

    return $config;
}
