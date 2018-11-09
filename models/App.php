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
    # use \October\Rain\Database\Traits\Purgeable;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'awebsome_serverpilot_apps';

    /**
     * @var array Attributes to be cast to JSON
     */
    protected $jsonable = ['autossl', 'ssl', 'domains'];

    /**
     * @var array Relations
     */
    public $hasMany = [
        'databases' => [Database::class, 'key' => 'app_api_id', 'otherKey' => 'api_id'],
    ];
    public $belongsTo = [
        'server'    => [Server::class, 'key' => 'server_api_id', 'otherKey' => 'api_id'],
        'sysuser'   => [Sysuser::class, 'key' => 'sysuser_api_id', 'otherKey' => 'api_id'],
    ];

    /**
     * @var boolean Flags that the model is currently being imported
     */
    public $importing;

    public function __construct(array $attributes = array())
    {
        /**
         * set default selected.
         */
        $this->setRawAttributes(['runtime' => CFG::get('runtime')], true);

        parent::__construct($attributes);
    }

    /**
     * Runs before the model is deleted, cleans up by deleting all related databases and tells ServerPilot to delete the app
     *
     * @return void
     */
    public function beforeDelete()
    {
        // Delete all databases of this app
        $this->databases()->delete();

        // Delete the app in serverpilot
        ServerPilot::apps($this->api_id)->delete();
    }

    /**
     * Runs before the model is updated in order to sync the settings with ServerPilot
     *
     * @return void
     */
    public function beforeUpdate()
    {
        if (!$this->importing) {
            ServerPilot::apps($this->api_id)->update([
                'runtime' => $this->runtime,
                'domains' => $this->api_domains
            ]);

            $app = App::find($this->id);

            if ($app->auto_ssl != $this->auto_ssl) {
                ServerPilot::apps($this->api_id)->autoSSL($this->auto_ssl);
            }

            if (!$this->auto_ssl) {
                $this->force_ssl = false;
            }

            if ($this->auto_ssl && $app->force_ssl != $this->force_ssl) {
                ServerPilot::apps($this->api_id)->forceSSL($this->force_ssl);
            }
        }
    }

    /**
     * Runs before the model is created in order to sync the settings with ServerPilot
     *
     * @return void
     */
    public function beforeCreate()
    {
        if (!$this->importing)
        {
            $app = ServerPilot::apps()->create([
                'name'      => $this->name,
                'sysuserid' => $this->sysuser_api_id,
                'runtime'   => $this->runtime,
                'domains'   => $this->api_domains
            ]);

            if ($app = @$app->data) {
                $app = ServerPilot::apps($app->id)->get()->data;
                $this->api_id = $app->id;
                $this->server_api_id = $app->serverid;
                $this->ssl = $app->ssl;
                $this->autossl = @$app->autossl;
                $this->datecreated = $app->datecreated;
            }
        }
    }


    /**
     * setAvailableSSL
     * ===============================================
     * @param array data from app->autossl->available
     * @return boolean is_enabled
     */
    public static function setAvailableSSL($autossl)
    {
        return (@$autossl->available == true) ? true : false;
    }

    /**
     * setAutoSSL
     * =========================================
     * @param array data from app->ssl->auto
     * @return boolean is_enabled
     */
    public static function setAutoSSL($ssl)
    {
        return (@$ssl->auto == true) ? true : false;
    }

    /**
     * setForceSSL
     * =========================================
     * @param array data from app->ssl->force
     * @return boolean is_enabled
     */
    public static function setForceSSL($ssl)
    {
        return (@$ssl->force == true) ? true : false;
    }

    /**
     * formmated data
     * ==========================================
     */
    public function getServerNameAttribute()
    {
        return $this->server->name;
    }


    /**
     * setDomains
     * ======================================
     * reformat array to repeater field
     * @param  array  $domains
     * @return array
     */
    public static function setDomains($domains)
    {
        $allDomains = [];
        foreach ($domains as $key => $value)
        {
           $allDomains[]['domain'] = $value;
        }
        return $allDomains;
    }

    /**
     * getDomains
     * ======================================
     * reformat array to api format
     * @return array
     */
    public function getApiDomainsAttribute()
    {
        return @array_column($this->domains, 'domain');
    }

    /**
     * get System Users availables
     * =============================================
     * @return array Sysuser for dropdown
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

    /**
     * get Runtimes availables
     * =============================================
     * @return array Runtime for dropdown
     */
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
