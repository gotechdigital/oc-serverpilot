<?php namespace Awebsome\Serverpilot\Classes;

use Awebsome\ServerPilot\Models\Settings as CFG;
use Awebsome\Serverpilot\Classes\Api\Curl;
use Awebsome\Serverpilot\Classes\ImportHandler as Import;

class ServerPilot extends Curl
{
    // constants
    const SP_CACHE              = 3600;
    const SP_TIMEOUT            = 30;
    const SP_API_ENDPOINT       = 'https://api.serverpilot.io/v1';
    const SP_USERAGENT          = 'serverpilot-api-php';
    const SP_HTTP_METHOD_POST   = 'POST';
    const SP_HTTP_METHOD_GET    = 'GET';
    const SP_HTTP_METHOD_DELETE    = 'DELETE';


    /**  Location for overloaded data.  */
    public $data;               # data for create, update, or delete.
    public $endpoint;           # resource to /endpoint
    public $name;               # $resource name. ex: apps, servers, dbs

    public $model;           # Models of resources.
    public $table;           # Models of resources.
    public $id;              # Model of resource.

    /**
     * Register Models.
     */
    public function registerModels()
    {
        return [
            'servers'   => 'Awebsome\ServerPilot\Models\Server',
            'sysusers'  => 'Awebsome\ServerPilot\Models\Sysuser',
            'apps'      => 'Awebsome\ServerPilot\Models\App',
            'dbs'       => 'Awebsome\ServerPilot\Models\Database',
            'actions'   => 'Awebsome\ServerPilot\Models\Action',
        ];
    }

    /**
     * Register tables.
     * ==========================================
     * mapped data between api and data tables.
     */
    public function registerTables()
    {
        return [
            'servers' => [
                # 'table_col'           => [api_key, mutatorMethod]
                'api_id'                => ['id'],
                'name'                  => ['name'],
                'autoupdates'           => ['autoupdates'],
                'firewall'              => ['firewall'],
                'lastaddress'           => ['lastaddress'],
                'datecreated'           => ['datecreated'],
                'lastconn'              => ['lastconn'],
                'created_at'            => ['datecreated'],
                'deny_unknown_domains'  => ['deny_unknown_domains']
            ],
            'sysusers' => [
                'api_id'                => ['id'],
                'server_api_id'         => ['serverid'],
                'name'                  => ['name']
            ],

            'dbs' => [
                'api_id'            => ['id'],
                'app_api_id'        => ['appid'],
                'server_api_id'     => ['serverid'],
                'name'              => ['name'],
                'user'              => ['user']
            ],

            'apps' => [
                'api_id'            => ['id'],
                'sysuser_api_id'    => ['sysuserid'],
                'server_api_id'     => ['serverid'],
                'name'              => ['name'],
                'runtime'           => ['runtime'],
                'ssl'               => ['ssl'],
                'autossl'           => ['autossl'],
                'domains'           => ['domains', 'setDomains'], //method to proccess data.
                'datecreated'       => ['datecreated']
            ],

            'actions' => [
                'api_id'            => ['id'],
                'server_api_id'     => ['serverid'],
                'status'            => ['status'],
                'datecreated'       => ['datecreated']
            ]
        ];
    }

    public function getModel($resource)
    {
        $models = $this->registerModels();
        return $models[$resource];
    }

    public function getTable($resource)
    {
        $tables = $this->registerTables();
        return $tables[$resource];
    }

    /**
     * isAuth
     * ==============================
     * check authentication
     * @return boolean is auth
     */
    public static function isAuth()
    {
        if(CFG::get('CLIENT_ID') && CFG::get('API_KEY'))
        {

            $sp = new Self;
            $response = $sp->actions('1')->get();
            $code = @$response->error->code;

            if($code != 401)
                return true;
            else return false;

        }else return false;
    }

    /**
     * Servers
     * ==============================
     * Resources Methods ServerPilot
     * @param $id to retrive one.
     * @return array data endpoint resource id
    */
    public static function servers($id = null)
    {
        $sp = new Self;
        $sp->name = __FUNCTION__;
        $sp->model = $sp->getModel($sp->name);
        $sp->table = $sp->getTable($sp->name);
        $sp->id = $id;

        return $sp;
    }

    public static function apps($id = null)
    {
        $sp = new Self;
        $sp->name = __FUNCTION__;
        $sp->model = $sp->getModel($sp->name);
        $sp->table = $sp->getTable($sp->name);
        $sp->id = $id;

        return $sp;
    }

    public static function sysusers($id = null)
    {
        $sp = new Self;
        $sp->name = __FUNCTION__;
        $sp->model = $sp->getModel($sp->name);
        $sp->table = $sp->getTable($sp->name);
        $sp->id = $id;

        return $sp;
    }

    public static function dbs($id = null)
    {
        $sp = new Self;
        $sp->name = __FUNCTION__;
        $sp->model = $sp->getModel($sp->name);
        $sp->table = $sp->getTable($sp->name);
        $sp->id = $id;

        return $sp;
    }

    public static function actions($id)
    {
        $sp = new Self;
        $sp->name = __FUNCTION__;
        $sp->model = $sp->getModel($sp->name);
        $sp->table = $sp->getTable($sp->name);
        $sp->id = $id;

        return $sp;
    }


    /**
     * Helper Methods
     */

    public function import($mode = null)
    {

        if(!$mode)
        {
            if(!$this->id)
                $import = Import::all($this);
            else
                $import = Import::import($this, $this->get()->data);
        }else if($mode == 'oneToOne')
        {
            $import = Import::allOneToOne($this);
        }

        return $import;
    }

    public function importBatch()
    {
        if(!$this->id)
        {
            $import = Import::batch($this);
            return $import;
        }
    }

    /**
     * CRUD METHODS
     */

    /**
     * get resource
     * ========================================================
     * get endpoint response.
     */
    public function get()
    {
        if(!$this->endpoint)
            $this->endpoint = ($this->id) ? $this->name . '/'.$this->id : $this->name;

        return $this->request($this->endpoint, $this->data);
    }

    public function update($data)
    {
        if($this->id && !$this->endpoint)
            $this->endpoint = $this->name . '/'.$this->id;

        return $this->request($this->endpoint, $data, self::SP_HTTP_METHOD_POST);
    }

    public function create($data)
    {
        return $this->request($this->name, $data, self::SP_HTTP_METHOD_POST);
    }

    public function delete()
    {
        $this->endpoint = $this->name . '/'.$this->id;

        $this->request($this->endpoint, null, self::SP_HTTP_METHOD_DELETE);
    }

    # update
    # delete
}
