<?php namespace Awebsome\Serverpilot;

use Event;
use Backend;
use Redirect;

use Awebsome\Serverpilot\Models\Sync;
use Awebsome\Serverpilot\Models\Settings as CFG;

use Awebsome\Serverpilot\Classes\ServerPilot;

use System\Classes\PluginBase;
use Db;

class Plugin extends PluginBase
{
    /**
     * @var array Plugin dependencies
     */
    //public $require = ['RainLab.Translate'];


    public function boot()
    {
        Event::listen('awebsome.serverpilot.afterSaveSettings', function() {
            // Code to register $user->email to mailing list
            if(ServerPilot::isAuth())
            {
                ServerPilot::servers()->import();
                ServerPilot::sysusers()->import();
                ServerPilot::apps()->import('oneToOne');
                ServerPilot::dbs()->import();
            }
        });
    }

    public function registerSchedule($schedule)
    {

    }

    public function registerSettings()
    {

        return [

            //Connection Settings
            'serverpilot'  => [
                'label'       => 'ServerPilot',
                'description' => 'Settings of ServerPilot',
                'category'    => 'ServerPilot',
                'icon'        => 'icon-cloud',
                'class'       => 'Awebsome\Serverpilot\Models\Settings',
                'order'       => 100,
                'permissions' => [ 'awebsome.serverpilot.settings' ],
                'keywords'    => 'Server ServerPilot'
            ],
        ];
    }
}
