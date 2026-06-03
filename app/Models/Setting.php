<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'key', 'value', 'type',
    ];

    public function getValueAttribute($value)
    {
        if ($this->type == 'image') {
            // return Storage::disk('ocean')->url($value);
            return Storage::url($value);
        }

        return $value;
    }
}
