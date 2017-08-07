<?php namespace Awebsome\Serverpilot\Controllers;

use Flash;
use Redirect;
use Backend;
use BackendMenu;

use System\Helpers\DateTime;

use Backend\Classes\Controller;
use Awebsome\Serverpilot\Classes\ServerPilot;
use Awebsome\Serverpilot\Classes\ServerPilotSync;

use Awebsome\Serverpilot\Models\Sync;
use Awebsome\Serverpilot\Models\Server;

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
        $this->vars['Servers'] = new Server;

        $this->vars['lastSync'] = DateTime::timeSince(Sync::max('created_at'));

        $this->asExtension('ListController')->index();
    }

    public function onSync()
    {
        $Sync = new ServerPilotSync;
        $Sync->Databases()->now()->log('sync_databases');

        return $this->listRefresh('databases');
    }


    public function onCreateForm()
    {
        $this->asExtension('FormController')->create();

        return $this->makePartial('forms/new_database_form');
    }

    public function onCreate()
    {
        # Save before create in ServerPilot.
        $this->asExtension('FormController')->create_onSave();

        return $this->listRefresh('databases');
    }

    /**
     * onResetPassword
     * reset pass and Sync ServerPilot
     *
     * @return [type] [description]
     */
    public function onResetPsw()
    {
        $resource   = post('resource');
        $id         = post('resource_id');
        $usr        = post('user_name');
        $npsw       = str_random(16);

        $ServerPilot = new ServerPilot;

        if($id)
            $ServerPilot->Databases($id)->update([ 'user' => ['id' => $usr, 'password' => $npsw] ]);

        return [
            'newPassword' => $npsw,
            'resource_id' => $id,
            'user_name'   => $usr
        ];
    }
}
