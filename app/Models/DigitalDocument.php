<?php

namespace App\Models;

use App\Core\Database;

class DigitalDocument
{
    public static function create(array $data): string
    {
        $id = $data['id'] ?? Database::uuid();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        Database::execute(
            "INSERT INTO resource_digital_documents (id, shop_id, original_name, storage_path, mime_type, size_bytes, total_pages, expires_at, is_deleted)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)",
            [
                $id,
                $data['shop_id'],
                $data['original_name'],
                $data['storage_path'],
                $data['mime_type'],
                $data['size_bytes'],
                $data['total_pages'],
                $expiresAt
            ]
        );

        return $id;
    }

    public static function findById(string $id): ?array
    {
        return Database::fetch("SELECT * FROM resource_digital_documents WHERE id = ? AND is_deleted = 0 LIMIT 1", [$id]);
    }

    public static function cleanupExpired(): int
    {
        $now = date('Y-m-d H:i:s');
        $expired = Database::fetchAll(
            "SELECT id, storage_path FROM resource_digital_documents WHERE expires_at < ? AND is_deleted = 0",
            [$now]
        );

        $count = 0;
        foreach ($expired as $doc) {
            $path = $doc['storage_path'];
            if (file_exists($path)) {
                @unlink($path);
            }
            Database::execute("UPDATE resource_digital_documents SET is_deleted = 1 WHERE id = ?", [$doc['id']]);
            $count++;
        }

        return $count;
    }
}

