<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersAttribute extends Model
{
    protected $table = 'users_attribute';

    public function users() {
        return $this->belongsTo('App\User');
    }
}
