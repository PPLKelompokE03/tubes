<?php

namespace App\Support;

use App\Models\User;

class RoleRedirect
{
    public static function routeFor(?User $user): string
    {
        return match ($user?->role) {
            'admin' => 'admin.dashboard',
            'seller' => 'seller.dashboard',
            default => 'dashboard',
        };
    }
}
