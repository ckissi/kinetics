<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Whether this user may read Pro content.
     *
     * Kinetics Pro is sold two ways, an ongoing subscription and a one-time
     * lifetime purchase, and either grants access. Neither exists yet: billing
     * arrives in its own milestone, and until the subscriptions and purchases
     * tables are here there is no way to hold access. Everything that gates Pro
     * content already routes through this method, so switching it on is a
     * single change rather than a hunt through the views.
     */
    public function hasProAccess(): bool
    {
        return false;
    }
}
