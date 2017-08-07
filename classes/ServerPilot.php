<?php namespace Awebsome\Serverpilot\Classes;

use ValidationException;
use  Awebsome\Serverpilot\Classes\Curl;
use  Awebsome\Serverpilot\Classes\ServerPilotSync;
use  Awebsome\Serverpilot\Models\Settings;

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
    public $data;
    public $path;
    public $auth;
    public $response;
    public $resource;


    public function __construct()
    {
        $CLIENT_ID = Settings::get('CLIENT_ID');
        $API_KEY = Settings::get('API_KEY');

        $this->auth = $CLIENT_ID.':'.$API_KEY;
        $this->data = [];
    }

    /**
     * Servers
     * ==============================
     * Resources Methods ServerPilot
     * @param object json response
     */
    public function Servers($id = null)
    {
        $this->resource = 'Servers';
        $this->path = ($id) ? 'servers/'.$id : 'servers';

        $this->setRequest();
        return $this;
    }

    /**
     * SystemUsers
     * ==============================
     * Resources Methods ServerPilot
     * @param object json response
     */
    public function SystemUsers($id = null)
    {
        $this->resource = 'SystemUsers';
        $this->path = ($id) ? 'sysusers/'.$id : 'sysusers';

        $this->setRequest();
        return $this;
    }

    /**
     * Apps
     * ==============================
     * Resources Methods ServerPilot
     * @param object json response
     */
    public function Apps($id = null)
    {
        $this->resource = 'Apps';
        $this->path = ($id) ? 'apps/'.$id : 'apps';

        $this->setRequest();
        return $this;
    }

    /**
     * App SSL
     * ==============================
     * Resources Methods ServerPilot
     * @param object json response
     */
    public function AppSSL($id = null)
    {
        $this->resource = 'Apps';
        $this->path = 'apps/'.$id.'/ssl';

        $this->setRequest();
        return $this;
    }

    /**
     * Databases
     * ==============================
     * Resources Methods ServerPilot
     * @param object json response
     */
    public function Databases($id = null)
    {
        $this->resource = 'Databases';
        $this->path = ($id) ? 'dbs/'.$id : 'dbs';

        $this->setRequest();
        return $this;
    }

    /**
     * Where field_table by id
     */
    public function where($by = null, $id = null)
    {
            $results = $this->response;

            if(!is_null($by)) {
                foreach($results->data as $key => $result) {
                    if($result->$by != $id) unset($results->data[$key]);
                }
            }

            $this->response = $results;

        return $this;
    }

    /**
     * Resource
     * Call resource method by resource method...
     *
     * @param [type] $Resource [description]
     * @param [type] $id       [description]
     */
    public function Resource($Resource, $id = null)
    {
        $this->$Resource($id);

        return $this;
    }

    /**
     * listAll
     * all data of resource selected
     * @return object json
     */
    public function listAll()
    {
        return $this->response;
    }


    public function get()
    {
        return $this->response->data;
    }

    /**
     * setRequest
     * curl request.
     */
    private function setRequest()
    {
        $this->response = $this->request($this->auth,$this->path,$this->data);
    }

    /**
     * update
     * ===================================
     * Update Resource method
     * @param  array $data  all data to update
     * @return object json response
     */
    public function update($data)
    {

        $this->data = $data;
        $this->response =  $this->request($this->auth, $this->path, $this->data, self::SP_HTTP_METHOD_POST);

        $Sync = new ServerPilotSync;
        $Sync->putUpdateResource($this->resource, $data, $this->response);

        return $this->response;
    }

    /**
     * create
     * ===================================
     * Create a Resource
     * @param  array $data  all data to create
     * @return object json response
     */
    public function create($data)
    {

        $this->data = $data;
        $this->response =  $this->request($this->auth,$this->path,$this->data, self::SP_HTTP_METHOD_POST);

        # $Sync = new ServerPilotSync;
        # $Sync->putCreateResource($this->resource, $data, $this->response);

        return $this->response;
    }

    public function delete($data = null)
    {
        $deleteables = Settings::get('deleteable_resources');

        if(!is_array($deleteables))
            $deleteables = [];

        if(in_array($this->resource, $deleteables)){


            $this->response =  $this->request($this->auth,$this->path,$data, self::SP_HTTP_METHOD_DELETE);

            return $this->response;
        }else throw new ValidationException(['error_mesage' => $this->resource.' is not a resource available for delete.']);
    }
}
