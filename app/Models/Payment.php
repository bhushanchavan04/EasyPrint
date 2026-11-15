<?php

namespace App\Models;

use App\Core\Database;

class Payment
{
    public static function create(array $data): string
    {
        $id = $data['id'] ?? Database::uuid();

        Database::execute(
            "INSERT INTO event_payments (id, order_id, payment_method, amount, transaction_reference, is_verified)
             VALUES (?, ?, ?, ?, ?, ?)",
            [
                $id,
                $data['order_id'],
                $data['payment_method'],
                $data['amount'],
                $data['transaction_reference'] ?? null,
                $data['is_verified'] ?? 0
            ]
        );

        return $id;
    }

    public static function verify(string $orderId, ?string $ref = null): void
    {
        Database::execute(
            "UPDATE event_payments SET is_verified = 1, transaction_reference = COALESCE(?, transaction_reference) WHERE order_id = ?",
            [$ref, $orderId]
        );
    }
}

