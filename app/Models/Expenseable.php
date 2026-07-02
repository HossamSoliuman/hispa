<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expenseable extends Model
{
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
