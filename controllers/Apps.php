<?php namespace Awebsome\Serverpilot\Controllers;

use Backend;
use Redirect;
use Request;
use Flash;
use BackendMenu;

use System\Helpers\DateTime;

use Backend\Classes\Controller;
use Awebsome\Serverpilot\Classes\ServerPilot;
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

    public $ServerPilot;

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Awebsome.Serverpilot', 'serverpilot', 'apps');
        
        $this->ServerPilot = new ServerPilot;

        $this->addCss($this->assetsPath.'/modal-form.css');
    }


    public function update($recordId = null, $context = null)
    {

        $this->asExtension('FormController')->update($recordId, $context);
    }

    public function index()
    {  

        $this->vars['Apps'] = new App;

        $this->vars['lastSync'] = DateTime::timeSince(Sync::max('created_at'));

        $this->asExtension('ListController')->index();
        
        $this->bodyClass = 'compact-container';
    }

    public function onSync()
    {
        $Sync = new ServerPilotSync;
        $Sync->Apps()->now()->log('sync_apps');

        return Redirect::to(Backend::url('awebsome/serverpilot/apps'));
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

        return Redirect::to(Backend::url('awebsome/serverpilot/apps/update/'.App::where('name',post('App.name'))->first()->id));
    }

}