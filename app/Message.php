<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'from',
        'to',
        'message',
        'read'
    ];

    public function fromContact(){
        return $this->hasOne('App\User', 'id', 'from');
    }

    public function toContact(){
        return $this->hasOne('App\User', 'id', 'to');
    }
}
