<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Page extends Model
{
    protected $fillable = [
        'title_en',
        'title_ar',
        'slug',
        'status',
        'page_type',
        'body_en',
        'body_ar',
        'status',
        'response',
        'response_by',

    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function setTitleEnAttribute($value)
    {
        $this->attributes['title_en'] = $value;
        // $this->attributes['slug'] = ($value);
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = $value ?: Str::slug($this->title_en ?? $this->title_ar ?? Str::random(8));
    }

    public function scopeActive($q)
    {

        return $q->where('status', 1);
    }

    public function getTitleAttribute($value)
    {
        return $this->{'title_'.App::getLocale()};
    }

    public function getBodyAttribute($value)
    {
        return $this->{'body_'.App::getLocale()};
    }
}
