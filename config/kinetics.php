<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pro content path
    |--------------------------------------------------------------------------
    |
    | Absolute path to a checkout of the private kinetics-pro repository. It
    | must sit outside the web root: nothing under it should ever be
    | reachable over HTTP.
    |
    | Leave this empty and kinetics:sync-pro becomes a no-op, which is what an
    | open-source clone of the public repository gets.
    |
    */

    'pro_path' => env('KINETICS_PRO_PATH'),

    /*
    |--------------------------------------------------------------------------
    | Where the Pro call to action points
    |--------------------------------------------------------------------------
    |
    | Every "unlock" button in the interface links here. It is configurable so
    | that pointing at a real checkout later is one change rather than a hunt
    | through the views.
    |
    */

    'pro_url' => env('KINETICS_PRO_URL', '/pro'),

];
