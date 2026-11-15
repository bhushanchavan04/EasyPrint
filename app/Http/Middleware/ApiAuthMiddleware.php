<?php

namespace App\Http\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Models\DeviceAgent;

class ApiAuthMiddleware
{
    public function handle(Request $request, Response $response, array $params = []): bool
    {
        $token = $request->getBearerToken();
        if (!$token) {
            $response->json([
                'error' => 'Unauthorized',
                'message' => 'Missing Bearer authentication token.'
            ], 401);
            return false;
        }

        $device = DeviceAgent::findByToken($token);
        if (!$device) {
            $response->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid or revoked machine agent authentication token.'
            ], 401);
            return false;
        }

        // Attach device to request context via GLOBALS or static storage
        $GLOBALS['current_device'] = $device;
        return true;
    }
}

