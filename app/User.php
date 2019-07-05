<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    
    /**
     * The channels the user receives notification broadcasts on.
     * El canal donde el usuario recibe notificaciones
     * @return string
     */
    public function receivesBroadcastNotificationsOn()
    {
        return 'user.notifications.'.$this->id;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','phone','profile_img'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function contacts_one()
    {
        return $this->belongsToMany('App\User', 'networks', 'user_one_id','user_two_id');
    }

    public function contacts_two()
    {
        return $this->belongsToMany('App\User', 'networks', 'user_two_id','user_one_id');
    }
}
