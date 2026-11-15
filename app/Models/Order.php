<?php

namespace App\Models;

use App\Core\Database;

class Order
{
    public static function generateOrderNumber(): string
    {
        $date = date('Ymd');
        $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
        return "MP-{$date}-{$random}";
    }

    public static function create(array $data): string
    {
        $id = $data['id'] ?? Database::uuid();
        $orderNumber = $data['order_number'] ?? self::generateOrderNumber();

        Database::execute(
            "INSERT INTO event_orders (id, shop_id, order_number, customer_identifier, total_pages, total_amount, status)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $id,
                $data['shop_id'],
                $orderNumber,
                $data['customer_identifier'] ?? null,
                $data['total_pages'],
                $data['total_amount'],
                $data['status'] ?? 'AWAITING_PAYMENT'
            ]
        );

        return $id;
    }

    public static function findById(string $id): ?array
    {
        return Database::fetch("SELECT * FROM event_orders WHERE id = ? LIMIT 1", [$id]);
    }

    public static function findByOrderNumber(string $orderNumber): ?array
    {
        return Database::fetch("SELECT * FROM event_orders WHERE order_number = ? LIMIT 1", [trim($orderNumber)]);
    }

    public static function getShopOrders(string $shopId, int $limit = 50): array
    {
        return Database::fetchAll(
            "SELECT o.*, 
                    p.payment_method, p.transaction_reference, p.is_verified as payment_verified,
                    pe.status as print_status, pe.attempts as print_attempts,
                    d.original_name as file_name, d.mime_type, d.total_pages as doc_pages,
                    s.copies, s.color_mode, s.sides, s.paper_size
             FROM event_orders o
             LEFT JOIN event_payments p ON o.id = p.order_id
             LEFT JOIN event_print_executions pe ON o.id = pe.order_id
             LEFT JOIN event_order_specifications s ON o.id = s.order_id
             LEFT JOIN resource_digital_documents d ON s.document_id = d.id
             WHERE o.shop_id = ?
             ORDER BY o.created_at DESC
             LIMIT ?",
            [$shopId, $limit]
        );
    }

    public static function updateStatus(string $orderId, string $status): void
    {
        $now = date('Y-m-d H:i:s');
        if ($status === 'PRINTING' || $status === 'PAYMENT_VERIFIED') {
            Database::execute("UPDATE event_orders SET status = ?, confirmed_at = ? WHERE id = ?", [$status, $now, $orderId]);
        } elseif ($status === 'COMPLETED') {
            Database::execute("UPDATE event_orders SET status = ?, completed_at = ? WHERE id = ?", [$status, $now, $orderId]);
        } else {
            Database::execute("UPDATE event_orders SET status = ? WHERE id = ?", [$status, $orderId]);
        }
    }

    public static function getShopMetrics(string $shopId): array
    {
        $today = date('Y-m-d 00:00:00');

        $todayPrints = (int)(Database::fetch(
            "SELECT SUM(total_pages) as cnt FROM event_orders WHERE shop_id = ? AND status = 'COMPLETED' AND completed_at >= ?",
            [$shopId, $today]
        )['cnt'] ?? 0);

        $todayRevenue = (float)(Database::fetch(
            "SELECT SUM(total_amount) as rev FROM event_orders WHERE shop_id = ? AND status = 'COMPLETED' AND completed_at >= ?",
            [$shopId, $today]
        )['rev'] ?? 0.0);

        $pendingCount = (int)(Database::fetch(
            "SELECT COUNT(*) as cnt FROM event_orders WHERE shop_id = ? AND status IN ('AWAITING_PAYMENT', 'PAYMENT_VERIFIED', 'PRINTING')",
            [$shopId]
        )['cnt'] ?? 0);

        $totalCompleted = (int)(Database::fetch(
            "SELECT COUNT(*) as cnt FROM event_orders WHERE shop_id = ? AND status = 'COMPLETED'",
            [$shopId]
        )['cnt'] ?? 0);

        return [
            'today_prints' => $todayPrints,
            'today_revenue' => $todayRevenue,
            'pending_orders' => $pendingCount,
            'total_completed' => $totalCompleted,
        ];
    }
}

