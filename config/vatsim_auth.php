<?php

return [
    

    /**
     * Base authentication url
     * The home of VATSIM OAuth Interface
     */
    'base' => env('VATSIM_OAUTH_BASE', 'https://auth.vatsim.net'),

    /**
     * Our consumer id.
     */
    'id' => env('VATSIM_OAUTH_CLIENT', ''),

    /**
     * Our secret acces key.
     * Do not give this to anyone else or display it to your users. It must be kept server-side.
     */
    'secret' => env('VATSIM_OAUTH_SECRET'),

    /**
     * The datascopes to be retrieved
     */
    'scopes' => explode(',', env('VATSIM_OAUTH_SCOPES', 'full_name,email,vatsim_details,country')),

];
