<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Super-admin bootstrap password
    |--------------------------------------------------------------------------
    |
    | Read by absensi:create-super-admin so the command can run unattended, for
    | example during provisioning. Left null the command prompts for the value
    | without echoing it.
    |
    | This is intentionally not a command-line option: options appear in shell
    | history and in the process list, where any user on the machine can read
    | them.
    |
    */

    'super_admin_password' => env('ABSENSI_SUPER_ADMIN_PASSWORD'),

];
