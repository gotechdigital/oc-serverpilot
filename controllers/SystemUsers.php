<?php namespace Awebsome\Serverpilot\Controllers;

use Flash;
use Backend;
use Redirect;
use Request;
use BackendMenu;
use Backend\Classes\Controller;

use System\Helpers\DateTime;

use Awebsome\Serverpilot\Classes\ServerPilot;
use Awebsome\Serverpilot\Classes\ServerPilotSync;

use Awebsome\Serverpilot\Models\Sync;
use Awebsome\Serverpilot\Models\SystemUser;

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

    public $requiredPermissions = ['awebsome.serverpilot.systemusers'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Awebsome.Serverpilot', 'serverpilot', 'systemusers');
    }

    public function index()
    {
        $this->vars['SystemUsers'] = new SystemUser;

        $this->vars['lastSync'] = DateTime::timeSince(Sync::max('created_at'));


        $this->asExtension('ListController')->index();
    }


    /**
     * onSync SystemUsers
     * @return redirect
     */
    public function onSync()
    {
        $Sync = new ServerPilotSync;
        $Sync->SystemUsers()->now()->log('sync_systemusers');

        return $this->listRefresh('systemusers');
    }



    public function onCreateForm()
    {
        $this->asExtension('FormController')->create();

        return $this->makePartial('forms/new_system_user_form');
    }



    public function onCreate()
    {
        # Saved on database.
        $this->asExtension('FormController')->create_onSave();

        return $this->listRefresh('systemusers');
    }

    /**
     * onResetPassword
     * reset pass and Sync ServerPilot
     *
     * @return [type] [description]
     */
    public function onResetPsw()
    {
        $id         = post('resource_id');
        $resource   = post('resource');
        $newPassword = str_random(16);

        $ServerPilot = new ServerPilot;

        if($id){
            $ServerPilot->SystemUsers($id)->update(['password'=> $newPassword]);
            SystemUser::find($id)->update(['password'=> $newPassword]);
        }

        return [
            'newPassword' => $newPassword,
            'resource_id' => $id
        ];
    }
}
