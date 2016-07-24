<?php namespace Awebsome\Serverpilot\Controllers;

use Backend;
use Redirect;
use BackendMenu;
use ValidationException;
use Backend\Classes\Controller;

use Awebsome\Serverpilot\Classes\ServerPilot;
use Awebsome\Serverpilot\Classes\ServerPilotSync;
use Awebsome\Serverpilot\Models\Settings;
use Awebsome\Serverpilot\Models\Database;

/**
 * Servers Back-end Controller
 */
class Servers extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';  

    public $ServerPilot;

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Awebsome.Serverpilot', 'serverpilot', 'servers');

        $this->ServerPilot = new ServerPilot(Settings::get('CLIENT_ID'), Settings::get('API_KEY'));
    }


    public function index()
    {
        
        $this->vars['ServerPilot'] = $this->ServerPilot;
        $this->asExtension('ListController')->index();
    }

    public function onSync()
    {
        $Sync = new ServerPilotSync;
        $Sync->All();

        return Redirect::to(Backend::url('awebsome/serverpilot/servers'));
    }

    public function tests()
    {
         
         
        # $sp = new ServerPilot(Settings::get('CLIENT_ID'), Settings::get('API_KEY'));
        #return '<pre>'.json_encode($sp->Apps()->listAll()->data, JSON_PRETTY_PRINT).'</pre>';
        ##$Resource = '\Database';
        #$Resource = "Awebsome\Serverpilot\Models".$Resource;
        #$Resource = new $Resource;

        #return '<pre>'.json_encode($Resource::where('id','>','1')->get(), JSON_PRETTY_PRINT).'</pre>';
         $Sync = new ServerPilotSync;
         return json_encode($Sync->ServersTest()->now());
    }
}