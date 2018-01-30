<?php return [
    'plugin' => [
        'name' => 'ServerPilot',
        'description' => 'Manage your ServerPilot account and aplications from OctoberCMS.'
    ],

    'message' => [

        'authentication_fails'  => 'ServerPilot: Authentication failed',
        'authentication_fails_help'  => 'Please check <a href="'.Backend::url('system/settings/update/awebsome/serverpilot/serverpilot').'">your credentials</a>, if the problem persists try contacting <a target="_blank" href="https://serverpilot.io">ServerPilot</a>.',
    ],
    'error' => [
        '400' => 'Error 400: ServerPilot says, We could not understand your request. Typically missing a parameter or header. :data',
        '401' => 'Error 401: ServerPilot says, Either no authentication credentials were provided or they are invalid.',
        '402' => 'Error 402: ServerPilot says, Method is restricted to users on the Coach or Business plan.',
        '403' => 'Error 403: ServerPilot says, Typically when trying to alter or delete protected resources.',
        '404' => 'Error 404: ServerPilot says, You requested a resource that does not exist. :data',
        '409' => 'Error 409: ServerPilot says, Typically when trying creating a resource that already exists.',
        '500' => 'Error 500: ServerPilot says, Internal server error. Try again at a later time.',
    ]
];
