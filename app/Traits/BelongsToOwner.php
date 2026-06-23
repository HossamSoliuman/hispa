<?php

namespace App\Traits;

use App\Models\Scopes\OwnerScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Scopes a model's rows to the owner that created them.
 *
 * Master/reference data (ports, fish, units, ...) used to be global because the
 * system originally had a single owner. This trait makes every owner-facing
 * query automatically filter to the authenticated owner and stamps new rows
 * with that owner's id.
 */
trait BelongsToOwner
{
    public static function bootBelongsToOwner(): void
    {
        static::addGlobalScope(new OwnerScope);

        static::creating(function (self $model): void {
            if (empty($model->owner_id)) {
                $ownerId = OwnerScope::resolveOwnerId();

                if ($ownerId !== null) {
                    $model->owner_id = $ownerId;
                }
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
