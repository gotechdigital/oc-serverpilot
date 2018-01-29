<?php namespace Awebsome\Serverpilot\Controllers;

use Backend;
use Redirect;
use BackendMenu;
use ValidationException;

use System\Helpers\DateTime;

use Backend\Classes\Controller;
use Awebsome\Serverpilot\Classes\ServerPilot;
use Awebsome\Serverpilot\Classes\ServerPilotSync;

use Awebsome\Serverpilot\Models\Sync;
use Awebsome\Serverpilot\Models\Server;
use Awebsome\Serverpilot\Models\SystemUser;
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

    public $requiredPermissions = ['awebsome.serverpilot.servers'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Awebsome.Serverpilot', 'serverpilot', 'servers');

        $this->addCss($this->assetsPath.'/modal-form.css');
    }
}
