<?php namespace Awebsome\Serverpilot\Models;

use Log;
use Model;

use Awebsome\Serverpilot\Models\Runtime;
use Awebsome\Serverpilot\Models\Sysuser;
use Awebsome\Serverpilot\Models\Settings as CFG;
use Awebsome\Serverpilot\Classes\ServerPilot;

/**
 * App Model
 */
class App extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'awebsome_serverpilot_apps';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['*'];

    /**
     * @var array jSonables fields
     */
    protected $jsonable = ['autossl','ssl', 'domains'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'databases' => ['Awebsome\Serverpilot\Models\Database','key' => 'app_api_id', 'otherKey' => 'api_id'],
    ];
    public $belongsTo = [
        'server'   => ['Awebsome\Serverpilot\Models\Server','key' => 'server_api_id', 'otherKey' => 'api_id'],
        'sysuser'    => ['Awebsome\Serverpilot\Models\Sysuser','key' => 'sysuser_api_id', 'otherKey' => 'api_id'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];


    /**
     * check if it's an import
     * @param boolean
     */
    public $importing;

    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(['runtime' => CFG::get('runtime')], true);

        parent::__construct($attributes);
    }

    public function beforeCreate()
    {
        if(!$this->importing)
        {
            $app = ServerPilot::apps()->create([
                'name'      => $this->name,
                'sysuserid' => $this->sysuser_api_id,
                'runtime'   => $this->runtime,
                'domains'   => [post('App.domains')],
            ]);

            if($app = @$app->data)
            {
                $app = ServerPilot::apps($app->id)->get()->data;
                $this->api_id = $app->id;
                $this->server_api_id = $app->serverid;
                $this->ssl = $app->ssl;
                $this->autossl = @$app->autossl;
                $this->datecreated = $app->datecreated;
                Log::info('creado...'. json_encode($app));
            }
        }
    }

    /**
     * get Sysuser Options
     */
    public function getSysuserOptions()
    {
        $users = Sysuser::all();
        $options = [];
        $options[''] = 'Select a user';

        foreach ($users as $user) {
            $options[$user->api_id] = $user->server->name.' > '.$user->name;
        }

        return $options;
    }

    public function getRuntimeOptions()
    {
        $runtimes = Runtime::orderBy('id', 'desc')->get();

        $options = [];

        foreach ($runtimes as $runtime) {
            $options[$runtime->version] = $runtime->label;
        }

        return $options;
    }
}
