<?php

namespace App\Models;

use App\Traits\BelongsToOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Company extends Model
{
    use BelongsToOwner;

    protected $table = 'companies';

    protected $fillable = [
        'owner_id',
        'name_ar',
        'name_en',
        'cr_number',
        'record_number',
        'vat_number',
        'email',
        'phone',
        'address',
        'website',
        'logo',
    ];

    /**
     * Localised company name, falling back to the other locale when one side is
     * empty so the report header is never blank.
     */
    public function getNameAttribute(): ?string
    {
        if (app()->getLocale() === 'ar') {
            return $this->name_ar ?: $this->name_en;
        }

        return $this->name_en ?: $this->name_ar;
    }

    /**
     * Public URL of the uploaded logo, falling back to the hispa default when
     * none has been set. The raw `logo` column keeps the storage path (reports
     * resolve it against the public disk); this accessor is for <img src> usage
     * in the panel.
     */
    public function getLogoUrlAttribute(): string
    {
        $path = $this->logo;

        if (empty($path)) {
            return asset('default-logo.png');
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}
