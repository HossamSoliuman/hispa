<?php

namespace App\Models\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class OwnerScope implements Scope
{
    /**
     * Restrict owner-scoped master data to the currently authenticated owner.
     *
     * The scope only applies when the request belongs to an owner context
     * (the owner web guard, or an owner / sub-user authenticated via the API).
     * Admins, dalals, gov supervisors and guests are left unscoped so their
     * existing cross-owner behaviour is preserved.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $ownerId = static::resolveOwnerId();

        if ($ownerId !== null) {
            $builder->where($model->getTable().'.owner_id', $ownerId);
        }
    }

    /**
     * Resolve the owner id for the current request, if any.
     */
    public static function resolveOwnerId(): ?int
    {
        foreach (['owner', 'sanctum'] as $guard) {
            $user = Auth::guard($guard)->user();

            if (! $user instanceof User) {
                continue;
            }

            if ($user->role === 'owner') {
                return (int) $user->id;
            }

            if (! empty($user->owner_id)) {
                return (int) $user->owner_id;
            }
        }

        return null;
    }
}
