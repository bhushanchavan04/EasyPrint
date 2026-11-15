<?php

/**
 * Standalone Database Setup / Migration Script
 */

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;
use App\Core\Env;

echo "Connecting to Database...\n";

try {
    $connection = Env::get('DB_CONNECTION', 'mysql');
    $db = Database::connect();
    echo " Connected to database successfully ({$connection}).\n\n";

    $isSqlite = ($connection === 'sqlite' || $db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite');

    echo "Applying schema...\n";
    $schemaFile = $isSqlite ? __DIR__ . '/schema_sqlite.sql' : __DIR__ . '/schema.sql';
    $schema = file_get_contents($schemaFile);
    foreach (explode(';', $schema) as $stmt) {
        $stmt = trim($stmt);
        if (!empty($stmt)) {
            $db->exec($stmt);
        }
    }
    echo " Tables created successfully.\n\n";

    echo "Applying seeders...\n";
    $seedersFile = $isSqlite ? __DIR__ . '/seeders_sqlite.sql' : __DIR__ . '/seeders.sql';
    $seeders = file_get_contents($seedersFile);
    foreach (explode(';', $seeders) as $stmt) {
        $stmt = trim($stmt);
        if (!empty($stmt)) {
            $db->exec($stmt);
        }
    }
    echo " Seed data inserted successfully.\n\n";

    echo "EasyPrint database initialization complete!\n";
} catch (\Throwable $e) {
    echo "Database initialization error: " . $e->getMessage() . "\n";
    exit(1);
}

