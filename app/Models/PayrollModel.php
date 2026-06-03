<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollModel extends Model
{
    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(PayrollDetailsModel::class, 'payroll_id');
    }
}
