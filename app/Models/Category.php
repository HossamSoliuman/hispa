<?php

namespace App\Models;

use App\Traits\BelongsToOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use BelongsToOwner, SoftDeletes;

    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'type',
        'status',
        'parent_id',
        'owner_id',
    ];

    protected $appends = ['name'];

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // علاقة بالمستخدمين (كمورد)
    public function users()
    {
        return $this->hasMany(User::class)->where('role', 'vendor');
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function getNameAttribute()
    {
        if (app()->getLocale() == 'en') {
            return $this->name_en ?? $this->name_ar;
        }

        return $this->name_ar ?? $this->name_en;
    }

    public function allExpenses()
    {
        return $this->hasManyThrough(
            Expense::class,
            Category::class,
            'parent_id',
            'category_id',
            'id',
            'id'
        )->union($this->expenses());
    }
}
