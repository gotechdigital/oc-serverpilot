<?php namespace Awebsome\Serverpilot;

use Awebsome\Serverpilot\Models\Sync;
use Awebsome\Serverpilot\Models\Settings;

use Awebsome\Serverpilot\Classes\ServerPilotSync;

use System\Classes\PluginBase;
use Db;

class Plugin extends PluginBase
{
    /**
     * @var array Plugin dependencies
     */
    public $require = ['RainLab.Translate'];

    public function registerComponents()
    {
    }

    public function registerSchedule($schedule)
    {
        $runTime = Settings::get('sync_data');

        if(!is_null($runTime))
        {
          if($runTime != 'realtime')
          {
              $schedule->call(function () {

                  $Sync = new ServerPilotSync;
                  $Sync->All()->log('sync_auto_schedule');

              })->$runTime();
          }
        }
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
