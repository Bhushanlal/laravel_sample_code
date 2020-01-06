<?php

namespace App;

use Laravel\Cashier\Billable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable {

    use HasApiTokens,
        Notifiable;
    use Sortable;
    // use SoftDeletes;
    use Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_role_id',
        'username',
        'parent_id',
        'country_id',
        'province_id',
        'city_id',
        'subscription_plan_id',
        'email_verified_at',
        'email',
        'first_name',
        'parent_id',
        'user_role_id',
        'last_name',
        'password',
        'advertiser_password',
        'phone',
        'mobile',
        'facebook_id',
        'linkedin_id',
        'user_url',
        'status',
        'mark_as_broker',
        /*        'stripe_id',
          'card_brand',
          'card_last_four',
          'trial_ends_at', */
        'deleted_at',
        'expiry_date',
        'is_old'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public $sortable = ['id', 'first_name', 'last_name', 'phone', 'email', 'username','expiry_date'];

    public function UserRoles() {
        return $this->belongsTo('App\UserRoles', 'user_role_id');
    }

    public function userProfile() {
        return $this->hasOne('App\UserProfile', 'user_id');
    }

    public function userProvince() {
        return $this->belongsTo('App\Province', 'province_id');
    }

    public function userCity() {
        return $this->belongsTo('App\Cities', 'city_id');
    }

    /*public function subscription() {
        return $this->belongsTo('App\Province', 'province_id');
    }*/

    // Authenticate from both 'username' and 'email' parameters
    public function findForPassport($identifier) {
        return $this->orWhere('email', $identifier)->orWhere('username', $identifier)->first();
    }

    public function userCountry() {
        return $this->belongsTo('App\Country', 'country_id');
    }

    public function userSubscriptions() {
        return $this->hasOne('App\Subscription', 'user_id');
    }

    public function getFullNameAttribute($value)
    {
      return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

}
