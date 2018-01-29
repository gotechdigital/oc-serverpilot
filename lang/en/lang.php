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
        '400' => 'ServerPilot: We could not understand your request. Typically missing a parameter or header.',
        '401' => 'ServerPilot: Either no authentication credentials were provided or they are invalid.',
        '402' => 'ServerPilot: Method is restricted to users on the Coach or Business plan.',
        '403' => 'ServerPilot: Typically when trying to alter or delete protected resources.',
        '404' => 'ServerPilot: You requested a resource that does not exist.',
        '409' => 'ServerPilot: Typically when trying creating a resource that already exists.',
        '500' => 'ServerPilot: Internal server error. Try again at a later time.',
    ]
];
