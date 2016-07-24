<?php namespace Awebsome\Serverpilot\Controllers;

use Backend;
use Redirect;
use BackendMenu;
use Backend\Classes\Controller;

use Awebsome\Serverpilot\Classes\ServerPilot;
use Awebsome\Serverpilot\Classes\ServerPilotSync;

use Awebsome\Serverpilot\Models\Settings;

/**
 * Users Back-end Controller
 */
class SystemUsers extends Controller
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

        BackendMenu::setContext('Awebsome.Serverpilot', 'serverpilot', 'systemusers');

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

        return Redirect::to(Backend::url('awebsome/serverpilot/systemusers'));
    }
}