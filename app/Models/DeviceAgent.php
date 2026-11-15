<?php

namespace App\Models;

use App\Core\Database;

class DeviceAgent
{
    public static function findById(string $id): ?array
    {
        return Database::fetch("SELECT * FROM device_agents WHERE id = ? LIMIT 1", [$id]);
    }

    public static function findByShopId(string $shopId): array
    {
        return Database::fetchAll("SELECT * FROM device_agents WHERE shop_id = ? ORDER BY is_online DESC, last_ping_at DESC", [$shopId]);
    }

    public static function generatePairingCode(string $shopId, string $deviceName = 'Windows PC'): string
    {
        $code = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        $id = Database::uuid();

        Database::execute(
            "INSERT INTO device_agents (id, shop_id, device_name, pairing_code, pairing_code_expires_at, is_online)
             VALUES (?, ?, ?, ?, ?, 0)",
            [$id, $shopId, $deviceName, $code, $expiresAt]
        );

        return $code;
    }

    public static function verifyPairingCode(string $code, string $deviceName): ?array
    {
        $now = date('Y-m-d H:i:s');
        $agent = Database::fetch(
            "SELECT d.*, s.name as shop_name, s.slug as shop_slug
             FROM device_agents d
             JOIN shops s ON d.shop_id = s.id
             WHERE d.pairing_code = ? 
               AND d.pairing_code_expires_at >= ?
             LIMIT 1",
            [$code, $now]
        );

        if (!$agent) {
            return null;
        }

        // Generate strong auth token
        $rawToken = 'epa_' . bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $rawToken);

        Database::execute(
            "UPDATE device_agents 
             SET auth_token_hash = ?, 
                 device_name = ?, 
                 pairing_code = NULL, 
                 pairing_code_expires_at = NULL, 
                 is_online = 1, 
                 last_ping_at = ? 
             WHERE id = ?",
            [$tokenHash, $deviceName, $now, $agent['id']]
        );

        $agent['raw_token'] = $rawToken;
        return $agent;
    }

    public static function findByToken(string $rawToken): ?array
    {
        $tokenHash = hash('sha256', $rawToken);
        $agent = Database::fetch(
            "SELECT d.*, s.id as shop_id, s.name as shop_name, s.slug as shop_slug, s.autoprint
             FROM device_agents d
             JOIN shops s ON d.shop_id = s.id
             WHERE d.auth_token_hash = ?
             LIMIT 1",
            [$tokenHash]
        );

        if ($agent) {
            $now = date('Y-m-d H:i:s');
            // Keep device online
            Database::execute("UPDATE device_agents SET is_online = 1, last_ping_at = ? WHERE id = ?", [$now, $agent['id']]);
        }

        return $agent;
    }
}

