<?php

namespace App\Models;

use App\Core\Database;

class ResourcePricingRule
{
    public static function getShopMatrix(string $shopId): array
    {
        return Database::fetchAll(
            "SELECT * FROM resource_pricing_rules WHERE shop_id = ? ORDER BY paper_size, color_mode, sides",
            [$shopId]
        );
    }

    public static function getRate(string $shopId, string $paperSize, string $colorMode, string $sides): float
    {
        $rule = Database::fetch(
            "SELECT price_per_page FROM resource_pricing_rules 
             WHERE shop_id = ? AND paper_size = ? AND color_mode = ? AND sides = ? 
             LIMIT 1",
            [$shopId, $paperSize, $colorMode, $sides]
        );

        if ($rule) {
            return (float)$rule['price_per_page'];
        }

        // Fallbacks if rule is missing
        if ($colorMode === 'COLOR') {
            return $sides === 'DOUBLE' ? 18.00 : 10.00;
        }
        return $sides === 'DOUBLE' ? 3.00 : 2.00;
    }

    public static function saveRules(string $shopId, array $rules): void
    {
        foreach ($rules as $r) {
            $paperSize = $r['paper_size'];
            $colorMode = $r['color_mode'];
            $sides = $r['sides'];
            $price = (float)$r['price_per_page'];

            $existing = Database::fetch(
                "SELECT id FROM resource_pricing_rules WHERE shop_id = ? AND paper_size = ? AND color_mode = ? AND sides = ? LIMIT 1",
                [$shopId, $paperSize, $colorMode, $sides]
            );

            if ($existing) {
                Database::execute(
                    "UPDATE resource_pricing_rules SET price_per_page = ? WHERE id = ?",
                    [$price, $existing['id']]
                );
            } else {
                Database::execute(
                    "INSERT INTO resource_pricing_rules (shop_id, paper_size, color_mode, sides, price_per_page) VALUES (?, ?, ?, ?, ?)",
                    [$shopId, $paperSize, $colorMode, $sides, $price]
                );
            }
        }
    }
}

