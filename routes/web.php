<?php

use App\Models\Effect;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

// Where every "unlock" button points. Real pricing and checkout arrive with
// billing; until then this explains what Pro is.
//
// The count comes from the effects, not from synced prompt rows: there is a
// prompt for every effect, and a checkout of the public repository that has
// never run kinetics:sync-pro would otherwise advertise none.
Route::get('/pro', fn() => view('pro', [
    'effectCount' => Effect::count(),
]))->name('pro');
