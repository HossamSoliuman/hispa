<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnModel extends Model
{
    protected $guarded = [];

    protected $table = 'returns';

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function details()
    {
        return $this->HasMany(ReturnDetail::class, 'return_id');
    }
}
