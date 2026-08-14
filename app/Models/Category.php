<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['slug', 'name', 'blurb', 'position'];

    public function effects(): HasMany
    {
        return $this->hasMany(Effect::class)->orderBy('position');
    }
}
