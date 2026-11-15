<?php

namespace App\Models;

use App\Core\Database;

class OrderSpecification
{
    public static function create(array $data): int
    {
        Database::execute(
            "INSERT INTO event_order_specifications (order_id, document_id, copies, color_mode, sides, paper_size, page_range, calculated_amount)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['order_id'],
                $data['document_id'],
                $data['copies'] ?? 1,
                $data['color_mode'] ?? 'BLACK_AND_WHITE',
                $data['sides'] ?? 'SINGLE',
                $data['paper_size'] ?? 'A4',
                $data['page_range'] ?? 'all',
                $data['calculated_amount']
            ]
        );

        return (int)Database::lastInsertId();
    }

    public static function findByOrderId(string $orderId): ?array
    {
        return Database::fetch(
            "SELECT s.*, d.original_name, d.storage_path, d.mime_type, d.total_pages as doc_pages
             FROM event_order_specifications s
             JOIN resource_digital_documents d ON s.document_id = d.id
             WHERE s.order_id = ?
             LIMIT 1",
            [$orderId]
        );
    }
}

