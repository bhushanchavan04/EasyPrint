<?php

namespace App\Http\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

class AuthMiddleware
{
    public function handle(Request $request, Response $response, array $params = []): bool
    {
        if (!Auth::check()) {
            $response->redirect('/login');
            return false;
        }

        return true;
    }
}

