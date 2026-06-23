<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\CheckRelationShip;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use CheckRelationShip, HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'boat_id',
        'logo',
        'roles_name',
        'status',
        'email',
        'email_verified_at',
        'password',
        'role',          // owner, captain, counter, dalal
        'owner_type',    // فقط للمالكين: fisherman / company
        'owner_id',
        'record_type',

        // حقول خاصة بالقبطان فقط
        'id_number',
        'nationality',
        'boat_name',
        'boat_number',
        'crew_count',
        'fcm_token',
        'boat_color',
        'boat_length',
        'boat_width',
        'region_id',
        'governorate_id',
        'city_id',
        'port_id',
        'commission_setting_id',

        'address',
        'website',
        'record_number',
        'tax_number',
        'cr_number',
        'vat_number',
        'attachment',
        'latitude',
        'longitude',

        'captain_id',
        'job_title',
        'date_appointment',
        'emergency_contact',
        'emergency_number',
        'residence_number',
        'residence_start_date',
        'residence_end_date',
        'passport_number',
        'id_attachment',
        'salary_type',
        'salary_amount',
        'profit_shares',
        'custom_share_percent',
        'bank_name',
        'account_number',
        'IBAN',

        // Vendor
        'company_name',
        'category_id',
        'notes',

        'fishing_license_number',
        'fishing_license_expiry',
        'driving_license_number',
        'driving_license_expiry',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'integer',
        'owner_id' => 'integer',
        'crew_count' => 'integer',
        'region_id' => 'integer',
        'governorate_id' => 'integer',
        'city_id' => 'integer',
        'port_id' => 'integer',
        'roles_name' => 'array',
    ];

    public function captainCount()
    {
        return User::where('role', 'captain')
            ->where('owner_id', $this->id) // to limit it per owner
            ->count();
    }

    public function getLogoAttribute($key)
    {

        // check if request is from laravel nova
        if ($key == '' || is_null($key)) {
            $firstLetter = strtoupper(mb_substr($this->name ?? 'U', 0, 1));

            return 'https://ui-avatars.com/api/?name='.$firstLetter.'&background=random&color=fff';
        }

        return Storage::url($key);
    }

    public function getAttachmentAttribute($key)
    {

        // check if request is from laravel nova
        if ($key == '' || is_null($key)) {
            return asset('uploads/default.jpg');
        } else {
            // return Storage::disk('ocean')->url($key);
            return Storage::url($key);
        }
    }

    public function getIdAttachmentAttribute($key)
    {

        // check if request is from laravel nova
        if ($key == '' || is_null($key)) {
            return asset('uploads/default.jpg');
        } else {
            //            return Storage::disk('ocean')->url($key);
            return Storage::url($key);
        }
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function scopeDisable(Builder $query): Builder
    {
        return $query->where('status', 0);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id')->withDefault();
    }

    public function captain()
    {
        return $this->belongsTo(User::class, 'captain_id')->withDefault();
    }

    /**
     * The owner's company profile (name, registration numbers, logo) shown on
     * printable reports. Owner-scoped, so each owner has at most one.
     */
    public function company(): HasOne
    {
        return $this->hasOne(Company::class, 'owner_id');
    }

    public function scopeCrewRole(Builder $query): Builder
    {
        return $query->where('role', 'crew');
    }

    public function scopeEmployeeRole(Builder $query): Builder
    {
        return $query->where('role', 'employee');
    }

    public function scopeGovRole(Builder $query): Builder
    {
        return $query->where('role', 'gov');
    }

    public function scopeOwnerRole(Builder $query): Builder
    {
        return $query->where('role', 'owner');
    }

    public function scopeCaptainRole(Builder $query): Builder
    {
        return $query->where('role', 'captain');
    }

    public function scopeDalalRole(Builder $query): Builder
    {
        return $query->where('role', 'dalal');
    }

    public function scopeCounterRole(Builder $query): Builder
    {
        return $query->where('role', 'counter');
    }

    public function boat()
    {
        return $this->belongsTo(Boat::class, 'boat_id')->withDefault();
    }

    /**
     * Boats owned by the user (owner role)
     */
    public function boats()
    {
        return $this->hasMany(Boat::class, 'owner_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class)->withDefault();
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class)->withDefault();
    }

    public function port()
    {
        return $this->belongsTo(Port::class)->withDefault();
    }

    public function contact()
    {
        return $this->hasMany(Contact::class);
    }

    public function getRelationNames(): array
    {
        return [
            'trips',
            'sales',
            // ضع هنا أسماء العلاقات الحقيقية فقط
        ];
    }

    public function category() // vendor
    {
        return $this->belongsTo(Category::class);
    }

    public function ownedExpenses()
    {
        return $this->hasMany(Expense::class, 'owner_id');
    }

    public function vendorExpenses()
    {
        return $this->hasMany(Expense::class, 'vendor_id');
    }

    public function maintenances()
    { // owner
        return $this->hasMany(Maintenance::class, 'owner_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')
            ->where('is_suspended', false)
            ->where('end_date', '>=', now());
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Trips owned by the user (owner role) – trip.owner_id.
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'owner_id');
    }

    /**
     * Sales where this user is the seller (owner/dalal/counter). For owners: seller_type=owner, seller_id=id.
     */
    public function salesAsSeller(): HasMany
    {
        return $this->hasMany(Sale::class, 'seller_id');
    }

    /**
     * Customers linked to this owner (owner_id).
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'owner_id');
    }
}
