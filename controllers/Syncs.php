<?php namespace Awebsome\Serverpilot\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

use Awebsome\Serverpilot\Models\Sync;

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

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Awebsome.Serverpilot', 'serverpilot', 'scheduling');
    }


    public function onSync()
    {
        $Sync = new ServerPilotSync;
        $Sync->All();

        return $this->listRefresh('syncs');
    }

    public function onClean()
    {
        $Sync = new Sync;
        $Sync->truncate();

        return $this->listRefresh('syncs');
    }
}