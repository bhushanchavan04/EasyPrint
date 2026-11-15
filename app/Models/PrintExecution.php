<?php

namespace App\Models;

use App\Core\Database;

class PrintExecution
{
    public static function create(string $orderId, ?string $deviceAgentId = null, ?string $printerId = null): string
    {
        $id = Database::uuid();

        Database::execute(
            "INSERT INTO event_print_executions (id, order_id, device_agent_id, printer_id, status, attempts)
             VALUES (?, ?, ?, ?, 'PENDING', 0)",
            [$id, $orderId, $deviceAgentId, $printerId]
        );

        return $id;
    }

    public static function getNextPendingForShop(string $shopId): ?array
    {
        Database::beginTransaction();
        try {
            $job = Database::fetch(
                "SELECT pe.id as execution_id, pe.order_id, pe.attempts,
                        o.order_number, o.total_pages,
                        s.copies, s.color_mode, s.sides, s.paper_size, s.page_range,
                        d.id as document_id, d.original_name as file_name, d.mime_type, d.storage_path,
                        rp.printer_name as target_printer, rp.system_spool_name
                 FROM event_print_executions pe
                 JOIN event_orders o ON pe.order_id = o.id
                 JOIN event_order_specifications s ON o.id = s.order_id
                 JOIN resource_digital_documents d ON s.document_id = d.id
                 LEFT JOIN resource_printers rp ON (rp.shop_id = o.shop_id AND rp.is_default = 1 AND rp.is_online = 1)
                 WHERE o.shop_id = ?
                   AND pe.status = 'PENDING'
                   AND o.status IN ('PRINTING', 'PAYMENT_VERIFIED')
                 ORDER BY pe.created_at ASC
                 LIMIT 1",
                [$shopId]
            );

            if ($job) {
                $now = date('Y-m-d H:i:s');
                Database::execute(
                    "UPDATE event_print_executions SET status = 'PROCESSING', attempts = attempts + 1, updated_at = ? WHERE id = ?",
                    [$now, $job['execution_id']]
                );
            }

            Database::commit();
            return $job;
        } catch (\Throwable $e) {
            Database::rollBack();
            return null;
        }
    }

    public static function updateStatus(string $executionId, string $status): void
    {
        $now = date('Y-m-d H:i:s');
        Database::execute(
            "UPDATE event_print_executions 
             SET status = ?, attempts = attempts + 1, updated_at = ? 
             WHERE id = ?",
            [$status, $now, $executionId]
        );
    }

    public static function findById(string $id): ?array
    {
        return Database::fetch("SELECT * FROM event_print_executions WHERE id = ? LIMIT 1", [$id]);
    }
}

