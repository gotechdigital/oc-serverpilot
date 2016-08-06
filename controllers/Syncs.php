<?php namespace Awebsome\Serverpilot\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

use System\Helpers\DateTime;

use Awebsome\Serverpilot\Models\Sync;
use Awebsome\Serverpilot\Models\Settings;
use Awebsome\Serverpilot\Classes\ServerPilot;
use Awebsome\Serverpilot\Classes\ServerPilotSync;

/**
 * Syncs Back-end Controller
 */
class Syncs extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',

    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = ['awebsome.serverpilot.syncs'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Awebsome.Serverpilot', 'serverpilot', 'scheduling');
    }

    public function index()
    {

        $this->vars['lastSync'] = DateTime::timeSince(Sync::max('created_at'));

        $this->asExtension('ListController')->index();
    }
    public function onSync()
    {
        $Sync = new ServerPilotSync;
        $Sync->All()->log('sync_all');
        
        return $this->listRefresh('syncs');
    }

    public function onClean()
    {
        $Sync = new Sync;
        $Sync->truncate();

        return $this->listRefresh('syncs');
    }

    public function test()
    {
        $test = Settings::get('sync_data');
        
        $allResponse = '<pre>'.json_encode($test, JSON_PRETTY_PRINT).'</pre>';
         return $allResponse;
    }
}