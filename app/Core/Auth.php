<?php

namespace App\Core;

use App\Models\Agent;
use App\Models\Shop;

class Auth
{
    private const SESSION_USER_KEY = '_auth_user_id';
    private const SESSION_SHOP_KEY = '_auth_shop_id';

    public static function check(): bool
    {
        return Session::has(self::SESSION_USER_KEY);
    }

    public static function user(): ?array
    {
        $id = Session::get(self::SESSION_USER_KEY);
        if (!$id) {
            return null;
        }
        return Agent::findById($id);
    }

    public static function shop(): ?array
    {
        $shopId = Session::get(self::SESSION_SHOP_KEY);
        if ($shopId) {
            return Shop::findById($shopId);
        }

        $user = self::user();
        if ($user) {
            $shop = Shop::findByOwnerId($user['id']);
            if ($shop) {
                Session::set(self::SESSION_SHOP_KEY, $shop['id']);
                return $shop;
            }
        }

        return null;
    }

    public static function id(): ?string
    {
        return Session::get(self::SESSION_USER_KEY);
    }

    public static function login(array $user): void
    {
        Session::set(self::SESSION_USER_KEY, $user['id']);
        $shop = Shop::findByOwnerId($user['id']);
        if ($shop) {
            Session::set(self::SESSION_SHOP_KEY, $shop['id']);
        }
    }

    public static function logout(): void
    {
        Session::remove(self::SESSION_USER_KEY);
        Session::remove(self::SESSION_SHOP_KEY);
        Session::destroy();
    }
}

