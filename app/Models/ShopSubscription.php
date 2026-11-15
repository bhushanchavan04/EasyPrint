<?php

namespace App\Models;

use App\Core\Database;

class ShopSubscription
{
    /**
     * Get currently active subscription for a shop (hard paywall validation)
     */
    public static function getActiveSubscription(string $shopId): ?array
    {
        $now = date('Y-m-d H:i:s');
        return Database::fetch(
            "SELECT s.*, p.name as plan_name, p.features, p.max_printers, p.max_monthly_prints
             FROM shop_subscriptions s
             JOIN subscription_plans p ON s.plan_id = p.id
             WHERE s.shop_id = ? 
               AND s.ends_at >= ? 
               AND s.status = 'ACTIVE'
             ORDER BY s.ends_at DESC 
             LIMIT 1",
            [$shopId, $now]
        );
    }

    public static function getLatest(string $shopId): ?array
    {
        return Database::fetch(
            "SELECT s.*, p.name as plan_name, p.price, p.duration_days, p.features
             FROM shop_subscriptions s
             JOIN subscription_plans p ON s.plan_id = p.id
             WHERE s.shop_id = ?
             ORDER BY s.created_at DESC
             LIMIT 1",
            [$shopId]
        );
    }

    public static function subscribe(string $shopId, string $planId, string $orderId, ?string $paymentId = null): string
    {
        $plan = SubscriptionPlan::findById($planId);
        if (!$plan) {
            throw new \InvalidArgumentException("Invalid subscription plan ID");
        }

        $id = Database::uuid();
        $days = (int)$plan['duration_days'];
        $startsAt = date('Y-m-d H:i:s');
        $endsAt = date('Y-m-d H:i:s', strtotime("+{$days} days"));

        Database::execute(
            "INSERT INTO shop_subscriptions (id, shop_id, plan_id, status, starts_at, ends_at, gateway_order_id, gateway_payment_id)
             VALUES (?, ?, ?, 'ACTIVE', ?, ?, ?, ?)",
            [
                $id,
                $shopId,
                $planId,
                $startsAt,
                $endsAt,
                $orderId,
                $paymentId
            ]
        );

        return $id;
    }

    public static function expireOutdated(): int
    {
        $now = date('Y-m-d H:i:s');
        return Database::execute(
            "UPDATE shop_subscriptions 
             SET status = 'EXPIRED' 
             WHERE ends_at < ? AND status IN ('ACTIVE', 'TRIAL')",
            [$now]
        );
    }
}

