<?php

namespace App\Models;

use App\Core\Database;

class SubscriptionPlan
{
    public static function allActive(): array
    {
        return Database::fetchAll("SELECT * FROM subscription_plans WHERE is_active = 1 ORDER BY price ASC");
    }

    public static function findById(string $id): ?array
    {
        return Database::fetch("SELECT * FROM subscription_plans WHERE id = ? LIMIT 1", [$id]);
    }
}

