<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expenseable extends Model
{
    use SoftDeletes;

    protected $table = 'expenseables';

    protected $fillable = ['expense_id', 'expenseable_type', 'expenseable_id'];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function expenseable()
    {
        return $this->morphTo();
    }
}
