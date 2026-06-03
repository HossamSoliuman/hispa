<?php

namespace App\Models;

use App\Observers\ContactObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(ContactObserver::class)]

class Contact extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'subject',
        'status',
        'message',
        'response',
        'response_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}
