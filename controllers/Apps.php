<?php namespace Awebsome\Serverpilot\Controllers;

use Backend;
use Redirect;
use Request;
use Flash;
use BackendMenu;

use System\Helpers\DateTime;

use Backend\Classes\Controller;
use Awebsome\Serverpilot\Classes\ServerPilotSync;

use Awebsome\Serverpilot\Models\Sync;
use Awebsome\Serverpilot\Models\App;

/**
 * Apps Back-end Controller
 */
class Apps extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $bodyClass = 'compact-container';
    protected $assetsPath = '/plugins/awebsome/serverpilot/assets';

    public $requiredPermissions = ['awebsome.serverpilot.apps'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Awebsome.Serverpilot', 'serverpilot', 'apps');

        $this->addCss($this->assetsPath.'/modal-form.css');
    }


    public function update($recordId = null, $context = null)
    {

        $this->asExtension('FormController')->update($recordId, $context);
    }


    public function onSync()
    {
        $Sync = new ServerPilotSync;
        $Sync->Apps()->now()->log('sync_apps');

        return $this->listRefresh('apps');
    }


    public function onCreateForm()
    {
        $this->asExtension('FormController')->create();

        return $this->makePartial('forms/new_app_form');
    }

    public function onCreate()
    {
        # Save before create in ServerPilot, to save and validations
        $this->asExtension('FormController')->create_onSave();

        # Redirect to APP Update page.
        $app = App::where('name', post('App.name'))->orderBy('created_at', 'desc')->first();

        return Redirect::to(Backend::url('awebsome/serverpilot/apps/update/'.$app->id));
    }

}
