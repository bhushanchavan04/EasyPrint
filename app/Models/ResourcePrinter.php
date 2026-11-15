<?php

namespace App\Models;

use App\Core\Database;

class ResourcePrinter
{
    /**
     * Common virtual or PDF printers that should never be registered as physical print endpoints
     */
    public const VIRTUAL_PRINTER_PATTERNS = [
        'microsoft print to pdf',
        'microsoft xps document writer',
        'onenote',
        'fax',
        'send to onenote',
        'adobe pdf',
        'cuteftp',
        'foxit reader pdf printer',
        'pdfcreator',
        'nitro pdf',
        'anydesk printer',
        'remote desktop easy print'
    ];

    public static function isVirtualPrinter(string $printerName): bool
    {
        $lower = strtolower(trim($printerName));
        foreach (self::VIRTUAL_PRINTER_PATTERNS as $pattern) {
            if (str_contains($lower, $pattern)) {
                return true;
            }
        }
        return false;
    }

    public static function findByShopId(string $shopId): array
    {
        return Database::fetchAll(
            "SELECT * FROM resource_printers WHERE shop_id = ? ORDER BY is_default DESC, printer_name ASC",
            [$shopId]
        );
    }

    public static function getDefaultPrinter(string $shopId): ?array
    {
        $printer = Database::fetch(
            "SELECT * FROM resource_printers WHERE shop_id = ? AND is_default = 1 AND is_online = 1 LIMIT 1",
            [$shopId]
        );

        if (!$printer) {
            $printer = Database::fetch(
                "SELECT * FROM resource_printers WHERE shop_id = ? AND is_online = 1 ORDER BY created_at ASC LIMIT 1",
                [$shopId]
            );
        }

        return $printer;
    }

    public static function syncPrinters(string $shopId, string $deviceAgentId, array $printersList): array
    {
        $registered = [];

        foreach ($printersList as $p) {
            $name = trim($p['name'] ?? '');
            if ($name === '' || self::isVirtualPrinter($name)) {
                continue;
            }

            $spoolName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
            $supportsColor = !empty($p['color']) ? 1 : 0;
            $supportsDuplex = !empty($p['duplex']) ? 1 : 0;
            $paperSizes = json_encode($p['paper_sizes'] ?? ['A4', 'LETTER', 'LEGAL']);

            // Check if exists
            $existing = Database::fetch(
                "SELECT id FROM resource_printers WHERE shop_id = ? AND printer_name = ? LIMIT 1",
                [$shopId, $name]
            );

            if ($existing) {
                Database::execute(
                    "UPDATE resource_printers 
                     SET device_agent_id = ?, system_spool_name = ?, is_online = 1, supports_color = ?, supports_duplex = ?, paper_sizes = ?
                     WHERE id = ?",
                    [$deviceAgentId, $spoolName, $supportsColor, $supportsDuplex, $paperSizes, $existing['id']]
                );
                $registered[] = $name;
            } else {
                $id = Database::uuid();
                // If it's the first printer, make it default
                $count = (int)Database::fetch("SELECT COUNT(*) as cnt FROM resource_printers WHERE shop_id = ?", [$shopId])['cnt'];
                $isDefault = ($count === 0 || str_contains(strtolower($name), 'laserjet')) ? 1 : 0;

                Database::execute(
                    "INSERT INTO resource_printers (id, shop_id, device_agent_id, printer_name, system_spool_name, is_default, is_online, supports_color, supports_duplex, paper_sizes)
                     VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?, ?)",
                    [$id, $shopId, $deviceAgentId, $name, $spoolName, $isDefault, $supportsColor, $supportsDuplex, $paperSizes]
                );
                $registered[] = $name;
            }
        }

        return $registered;
    }
}

