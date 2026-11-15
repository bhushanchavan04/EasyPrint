<?php

namespace App\Models;

use App\Core\Database;

class Shop
{
    public static function findById(string $id): ?array
    {
        return Database::fetch("SELECT * FROM shops WHERE id = ? LIMIT 1", [$id]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::fetch("SELECT * FROM shops WHERE slug = ? LIMIT 1", [trim(strtolower($slug))]);
    }

    public static function findByOwnerId(string $ownerId): ?array
    {
        return Database::fetch("SELECT * FROM shops WHERE owner_agent_id = ? LIMIT 1", [$ownerId]);
    }

    public static function create(array $data): string
    {
        $id = $data['id'] ?? Database::uuid();
        
        Database::execute(
            "INSERT INTO shops (id, owner_agent_id, name, slug, upi_id, upi_name, logo_url, accept_cash, autoprint, is_active)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $id,
                $data['owner_agent_id'],
                $data['name'],
                trim(strtolower($data['slug'])),
                $data['upi_id'],
                $data['upi_name'],
                $data['logo_url'] ?? null,
                $data['accept_cash'] ?? 1,
                $data['autoprint'] ?? 1,
                $data['is_active'] ?? 1
            ]
        );

        return $id;
    }

    public static function update(string $id, array $data): void
    {
        $fields = [];
        $values = [];
        foreach ($data as $key => $val) {
            $fields[] = "{$key} = ?";
            $values[] = $val;
        }
        $values[] = $id;

        $sql = "UPDATE shops SET " . implode(', ', $fields) . " WHERE id = ?";
        Database::execute($sql, $values);
    }
}

