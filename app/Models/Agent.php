<?php

namespace App\Models;

use App\Core\Database;

class Agent
{
    public static function findById(string $id): ?array
    {
        return Database::fetch("SELECT * FROM agents WHERE id = ? LIMIT 1", [$id]);
    }

    public static function findByEmail(string $email): ?array
    {
        return Database::fetch("SELECT * FROM agents WHERE email = ? LIMIT 1", [trim(strtolower($email))]);
    }

    public static function create(array $data): string
    {
        $id = $data['id'] ?? Database::uuid();
        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
        
        Database::execute(
            "INSERT INTO agents (id, agent_type, name, email, password_hash, phone) VALUES (?, ?, ?, ?, ?, ?)",
            [
                $id,
                $data['agent_type'] ?? 'SHOP_OWNER',
                $data['name'],
                trim(strtolower($data['email'])),
                $passwordHash,
                $data['phone'] ?? null
            ]
        );

        return $id;
    }

    public static function verifyPassword(array $agent, string $password): bool
    {
        return password_verify($password, $agent['password_hash']);
    }
}

