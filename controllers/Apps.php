<?php namespace Awebsome\Serverpilot\Controllers;

use Flash;
use Backend;
use Redirect;
use BackendMenu;
use ValidationException;

use Backend\Classes\Controller;

use Awebsome\Serverpilot\Classes\ServerPilot;
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

    public function index()
    {
        # if(ServerPilot::isAuth())
        #    ServerPilot::apps()->import();

        $this->asExtension('ListController')->index();
    }

    public function update($recordId, $context = null)
    {
        $app = App::find($recordId);

        if(ServerPilot::isAuth())
        $app = ServerPilot::apps($app->api_id)->import();
            if($app)
                Flash::info('APP updated from ServerPilot.');

        $this->asExtension('FormController')->update($recordId);
    }

    public function api($id = null)
    {
        $result = ServerPilot::apps($id)->get();

        //$result2 = ServerPilot::apps($id)->forceSSL(false);


        $print = '<pre>'.json_encode($result, JSON_PRETTY_PRINT).'</pre>';
        //$print.= '<pre>'.json_encode($result2, JSON_PRETTY_PRINT).'</pre>';
        return $print;
    }

    /**
     * New App from list Modal.
     */
    public function onCreateForm()
    {
        $this->asExtension('FormController')->create();
        return $this->makePartial('create_modal');
    }


    public function onCreate()
    {
        # Save before create in ServerPilot, to save and validations
        $this->asExtension('FormController')->create_onSave();

        # Redirect to APP Update page.
        $app = App::where('name', post('App.name'))->orderBy('created_at', 'desc')->first();

        return Redirect::to(Backend::url('awebsome/serverpilot/apps/update/'.$app->id));
    }


    public function test()
    {
        $result = App::find(1);
        $print = '<pre>'.json_encode($result, JSON_PRETTY_PRINT).'</pre>';
        return $print;
    }
}
