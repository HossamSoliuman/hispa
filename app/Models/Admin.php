<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, HasRoles,Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'logo',
        'password',
        'fcm_token',
        'status',
        'roles_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'roles_name' => 'array',
    ];

    public function getLogoAttribute($key)
    {

        // check if request is from laravel nova
        if ($key == '' || is_null($key)) {
            $firstLetter = strtoupper(mb_substr($this->name ?? 'U', 0, 1));

            return 'https://ui-avatars.com/api/?name='.$firstLetter.'&background=random&color=fff';
        } else {

            // return Storage::disk('ocean')->url($key);
            return Storage::url($key);
        }

    }
}
