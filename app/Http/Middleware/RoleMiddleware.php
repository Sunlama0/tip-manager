<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Guard;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role, $guard = null)
    {
        $authGuard = Auth::guard($guard);

        $user = $authGuard->user();

        if (! $user && $request->bearerToken() && config('permission.use_passport_client_credentials')) {
            $user = Guard::getPassportClient($guard);
        }

        if (! $user) {
            throw UnauthorizedException::notLoggedIn();
        }

        if (! method_exists($user, 'hasAnyRole')) {
            throw UnauthorizedException::missingTraitHasRoles($user);
        }

        $roles = explode('|', self::parseRolesToString($role));

        if (! $user->hasAnyRole($roles)) {
            throw UnauthorizedException::forRoles($roles);
        }

        return $next($request);
    }

    public static function using($role, $guard = null)
    {
        $roleString = self::parseRolesToString($role);
        $args = is_null($guard) ? $roleString : "$roleString,$guard";
        return static::class . ':' . $args;
    }

    protected static function parseRolesToString(array|string|\BackedEnum $role)
    {
        if ($role instanceof \BackedEnum) {
            $role = $role->value;
        }

        if (is_array($role)) {
            $role = array_map(fn($r) => $r instanceof \BackedEnum ? $r->value : $r, $role);
            return implode('|', $role);
        }

        return (string) $role;
    }
}
