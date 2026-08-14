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

];
