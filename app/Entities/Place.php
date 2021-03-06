<?php

namespace PokemonBuddy\Entities;

use Carbon\Carbon;
use Faker\Provider\zh_TW\DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth as Auth;

class Place extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'places';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'geo_lat', 'geo_lng', 'user_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['user_id', 'deleted_at', 'created_at', 'updated_at'];

    protected $dates = ['deleted_at', 'expire_at'];

    /**
     * A Place belongs to an User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('PokemonBuddy\Entities\User');
    }

    /**
     * A Place has many Stars.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stars()
    {
        return $this->hasMany('PokemonBuddy\Entities\PlaceStar');
    }

    /**
     * A Place has many Comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany('PokemonBuddy\Entities\PlaceComment');
    }

    /**
     * A Place has many Subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany('PokemonBuddy\Entities\Subscription');
    }

    public static function boot()
    {
        parent::boot();

        static::deleted(function ($place) {
            $place->stars()->delete();
            $place->comments()->delete();
            $place->subscriptions()->delete();
        });

        static::restored(function ($place) {
            $place->stars()->withTrashed()->restore();
            $place->comments()->withTrashed()->restore();
            $place->subscriptions()->withTrashed()->restore();
        });
    }

    public function getStarsAmountAttribute()
    {
        $starsForPlace = $this->starsWithTrashed()->getResults();

        return $starsForPlace->count();
    }

    public function getStarsAverageAttribute()
    {
        $starsForPlace = $this->starsWithTrashed()->getResults();

        if($starsForPlace->count() == 0) {
            return 0.00;
        }

        $average = $starsForPlace->sum('stars') / $starsForPlace->count();

        return number_format($average, 2);
    }

    public function getStarsProgressBarAttribute()
    {
        $starsForPlace = $this->starsWithTrashed()->getResults();

        if($starsForPlace->count() == 0) {
            return '0%';
        }

        $average = $starsForPlace->sum('stars') / $starsForPlace->count();
        $averagePercent = ($average / 5) * 100;

        return number_format($averagePercent, 0) . '%';
    }

    public function getUserHasVotedAttribute()
    {
        $starsForPlace = $this->starsWithTrashed()->getResults();

        foreach ($starsForPlace as $star) {
            if ($star->user->id == Auth::user()->id) {
                return $star->id;
            }
        }

        return false;
    }


    public function getIdOfUserStarAttribute()
    {
        $starsForPlace = $this->starsWithTrashed()->getResults();

        foreach ($starsForPlace as $star) {
            if ($star->user->id == Auth::user()->id) {
                return $star->id;
            }
        }

        return false;
    }

    public function getCurrentUserVoteAttribute()
    {
        $starsForPlace = $this->starsWithTrashed()->getResults();

        foreach ($starsForPlace as $star) {
            if ($star->user->id == Auth::user()->id) {
                return number_format($star->stars, 0);
            }
        }

        return -1;
    }



    public function getNumberOfCommentsAttribute()
    {
        $allComments = $this->commentsWithTrashed()->getResults();

        $countComments = 0;

        foreach ($allComments as $comments) {
            $countComments++;
        }

        return $countComments;
    }

    public function getIsAuthorAttribute()
    {
        $isAuthor = false;

        if (Auth::check()) {
            if (Auth::user()->id == $this->user_id) {
                $isAuthor = true;
            }
        }

        return $isAuthor;
    }

    public function starsWithTrashed()
    {
        if ($this->trashed()) {
            return $this->stars()->onlyTrashed();
        } else {
            return $this->stars();
        }
    }

    public function commentsWithTrashed()
    {
        if ($this->trashed()) {
            return $this->comments()->onlyTrashed();
        } else {
            return $this->comments();
        }
    }

    public function getIsSubscribedAttribute()
    {
        $isSubscribed = false;

        if (Auth::check()) {
            $subscription_number = Subscription::where('user_id',
                Auth::user()->id)->where('place_id', $this->id)->count();

            if ($subscription_number) {
                $isSubscribed = true;
            }
        }

        return $isSubscribed;
    }

    public function getIsActiveAttribute()
    {
        return $this->expire_at > Carbon::now();
    }

    public function getRemainTimeAttribute()
    {
        $remain_time = Carbon::now()->diff($this->expire_at);

        return implode(':', [$remain_time->h, $remain_time->i, $remain_time->s]);
    }

    public function getFastPokemapUrlAttribute()
    {
        return 'https://fastpokemap.se#' . $this->geo_lat . ',' . $this->geo_lng;
    }
}
