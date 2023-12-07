<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\WelcomeNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    # email verification notification
    public function sendVerificationNotification()
    {
        $this->notify(new EmailVerificationNotification());
    }

    # registration notification
    public function registrationNotification()
    {
        $this->notify(new WelcomeNotification());
    }

    # guarded
    protected $guarded = [
        ''
    ];

    # hidden for serializations
    protected $hidden = [
        'password',
        'remember_token',
    ];

    # should be casted
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    # role
    public function role()
    {
        return $this->belongsTo(SpatieRole::class);
    }

    # subscriptionPackage 
    public function subscriptionPackage()
    {
        return $this->belongsTo(SubscriptionPackage::class)->withTrashed();
    }

    # subscriptionHistories
    public function subscriptionHistories()
    {
        return $this->hasMany(SubscriptionHistory::class);
    }

    # referred users
    public function referredUsers()
    {
        return $this->hasMany(User::class, 'referred_by', 'id');
    }
    # created users
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withDefault([
            'name' => 'not found'
        ]);
    }

    # referred users earnings
    public function referredUserEarnings()
    {
        return $this->hasMany(AffiliateEarning::class, 'referred_by', 'id');
    }

    # affiliatePayoutAccounts
    public function affiliatePayoutAccounts()
    {
        return $this->hasMany(AffiliatePayoutAccount::class);
    }
    # non paid subscriptionHistories
    function nonPaidSubscriptionHistories()
    {
        return $this->subscriptionHistories()->where('payment_status', '!=', 1)->where('payment_method', 'offline');
    }
    #active package
    public function currentPackage()
    {
        return $this->hasOne(SubscriptionHistory::class, 'user_id', 'id')->where('subscription_status', 1);
    }
    # subscriped package
    public function subscribeds()
    {
        return $this->hasMany(SubscriptionHistory::class, 'user_id', 'id')->where('subscription_status', 3);
    }
    #avatar image
    public function profileImage()
    {
        return $this->belongsTo(MediaManager::class, 'avatar', 'id')->withDefault();
    }
}
