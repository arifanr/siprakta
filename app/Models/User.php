<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function usersAttributes() {
        return $this->hasMany('App\Models\UsersAttribute', 'users_id');
    }

    public function hasRole($role) {
        if ($this->usersAttributes()->where('attribute_name', 'role')->where('attribute_value', $role)->first()) {
            return true;
        }
        return false;
    }

    public function hasAnyRoles($roles) {
        if ($this->usersAttributes()->where('attribute_name', 'role')->whereIn('attribute_value', $roles)->first()) {
            return true;
        }
        return false;
    }
}
