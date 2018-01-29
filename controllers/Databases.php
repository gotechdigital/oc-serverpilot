<?php namespace Awebsome\Serverpilot\Controllers;

use Backend;
use BackendMenu;

use Backend\Classes\Controller;
use Awebsome\Serverpilot\Classes\ServerPilot;

/**
 * Databases Back-end Controller
 */
class Databases extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = ['awebsome.serverpilot.databases'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Awebsome.Serverpilot', 'serverpilot', 'databases');
    }

    public function index()
    {
        if(ServerPilot::isAuth())
            ServerPilot::dbs()->import();

        $this->asExtension('ListController')->index();
    }
}
