<?php namespace Awebsome\Serverpilot\Classes;

use Awebsome\Serverpilot\Classes\Api\Curl;
use Awebsome\Serverpilot\Classes\Synchronizer;

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
    public $resource;           # $resource name.

    public $models;              # Models of resources.
    public $model;              # Model of resource.
    public $id;              # Model of resource.

    public function __construct()
    {
        $this->registerModels();
    }

    public function registerModels()
    {
        /**
         * Register Models.
         */
        return $this->models = [
            'servers'   => 'Awebsome\ServerPilot\Models\Server',
            'sysusers'  => 'Awebsome\ServerPilot\Models\Sysuser',
            'apps'      => 'Awebsome\ServerPilot\Models\App',
            'dbs'       => 'Awebsome\ServerPilot\Models\Database',
            'actions'   => 'Awebsome\ServerPilot\Models\Action',
        ];
    }

    /**
     * isAuth
     * ==============================
     * check authentication
     * @return boolean is auth
     */
    public static function isAuth()
    {
        $sp = new Self;
        $response = $sp->actions('1')->get();
        $code = @$response->error->code;

        if($code != 401)
            return true;
        else return false;
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
        $sp->resource = __FUNCTION__;
        $sp->id = $id;

        return $sp;
    }

    public static function apps($id = null)
    {
        $sp = new Self;
        $sp->resource = __FUNCTION__;
        $sp->id = $id;

        return $sp;
    }

    public static function sysusers($id = null)
    {
        $sp = new Self;
        $sp->resource = __FUNCTION__;
        $sp->id = $id;

        return $sp;
    }

    public static function dbs($id = null)
    {
        $sp = new Self;
        $sp->resource = __FUNCTION__;
        $sp->id = $id;

        return $sp;
    }

    public static function actions($id)
    {
        $sp = new Self;
        $sp->resource = __FUNCTION__;
        $sp->id = $id;

        return $sp;
    }


    /**
     * Helper Methods
     */

    public function import()
    {
        $this->model = $this->models[$this->resource];

        if($this->id)
            $import = Synchronizer::import($this);
        else $import = Synchronizer::importAll($this);

        return $import;
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
            $this->endpoint = ($this->id) ? $this->resource . '/'.$this->id : $this->resource;

        return $this->request($this->endpoint, $this->data);
    }

    # update
    # delete
}
