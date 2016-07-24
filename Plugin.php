<?php namespace Awebsome\Serverpilot;

use Awebsome\Serverpilot\Models\Sync;
use Awebsome\Serverpilot\Models\Settings;

use System\Classes\PluginBase;
use Db;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
    }

    public function registerSchedule($schedule)
    {
        $runTime = Settings::get('sync_data');

        $schedule->call(function () {
            $Sync = new Sync;
            $Sync->schedule = 'sync_'.$runTime;
            $Sync->save();            

        })->$runTime();
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
                'permissions' => [ 'awebsome.serverpilot.configs' ],
                'keywords'    => 'Server ServerPilot'
            ],
        ];
    }
}
