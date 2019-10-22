<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $table = 'files';
    protected $fillable=[
        'filename',
        'mime',
        'path',
        'size',
        'read',
        'from',
        'to'
    ];
}
