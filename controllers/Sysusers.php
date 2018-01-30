<?php namespace Awebsome\Serverpilot\Controllers;

use Backend;
use BackendMenu;

use Backend\Classes\Controller;

use Awebsome\Serverpilot\Classes\ServerPilot;

/**
 * Servers Back-end Controller
 */
class Sysusers extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = ['awebsome.serverpilot.sysusers'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Awebsome.Serverpilot', 'serverpilot', 'sysusers');

        $this->addCss($this->assetsPath.'/modal-form.css');
    }

    public function index()
    {
        if(ServerPilot::isAuth())
            ServerPilot::sysusers()->import();

        $this->asExtension('ListController')->index();
    }

    public function api($id = null)
    {
        $result = ServerPilot::sysusers($id)->get();

        $print = '<pre>'.json_encode($result, JSON_PRETTY_PRINT).'</pre>';
        return $print;
    }
}
