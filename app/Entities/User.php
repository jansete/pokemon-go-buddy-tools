<?php

namespace PokemonBuddy\Entities;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name',
        'username',
        'email',
        'password',
        'language',
        'github_id',
        'facebook_id',
        'twitter_id',
        'avatar'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'email',
        'password',
        'remember_token',
        'language',
        'github_id',
        'facebook_id',
        'twitter_id',
        'avatar',
        'created_at',
        'updated_at',
        'token',
        'modified',
        'verified',
    ];

    /**
     * Boot the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->token = str_random(30);
        });
    }

    /**
     * An User has many Places.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function places()
    {
        return $this->hasMany('PokemonBuddy\Entities\Place');
    }

    /**
     * An User has many Stars.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stars()
    {
        return $this->hasMany('PokemonBuddy\Entities\PlaceStar');
    }

    /**
     * An User has many Comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany('PokemonBuddy\Entities\PlaceComment');
    }

    /**
     * An User has many Subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany('PokemonBuddy\Entities\Subscription');
    }

    public function StarsForThisPlace()
    {
        $stars = $this->stars()->getResults();
    }

    public function getNumberOfPlacesAttribute()
    {
        $numberOfPlaces = $this->places()->count();

        return $numberOfPlaces;
    }

    public function getNumberOfPlacesRatedAttribute()
    {
        $numberOfPlaces = $this->stars()->count();

        return $numberOfPlaces;
    }

    public function placesTrashed()
    {
        return $this->places()->onlyTrashed();
    }

    public function getNumberOfPlacesTrashedAttribute()
    {
        $numberOfPlacesTrashed = $this->placesTrashed()->count();

        return $numberOfPlacesTrashed;
    }

    /**
     * Confirm the user.
     *
     * @return void
     */
    public function confirmEmail()
    {
        $this->verified = true;
        $this->modified = false;
        $this->token = null;
        $this->save();
    }

    public function setLanguage($language)
    {
        $this->language = $language;
        $this->save();
    }
}
