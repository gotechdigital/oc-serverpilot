<?php namespace Awebsome\Serverpilot\Classes;

use  Awebsome\Serverpilot\Classes\Curl;

class ServerPilot extends Curl
{
    // constants
    const SP_CACHE              = 3600;
    const SP_TIMEOUT            = 30;
    const SP_API_ENDPOINT       = 'https://api.serverpilot.io/v1';
    const SP_USERAGENT          = 'serverpilot-api-php';
    const SP_HTTP_METHOD_POST   = 'POST';
    const SP_HTTP_METHOD_GET    = 'GET';
    #const SP_HTTP_METHOD_DELETE    = 'DELETE';
        

    /**  Location for overloaded data.  */
    public $data;
    public $path;
    public $auth;
    public $response;


    public function __construct($CLIENT_ID, $API_KEY)
    {
        $this->auth = $CLIENT_ID.':'.$API_KEY;
        $this->data = [];
    }

    public function Servers($id = null)
    {
        $this->path = ($id) ? 'servers/'.$id : 'servers';

        $this->setRequest();
        return $this;
    } 

    public function SystemUsers($id = null)
    {
        $this->path = ($id) ? 'sysusers/'.$id : 'sysusers';

        $this->setRequest();
        return $this;
    }

    public function Apps($id = null)
    {
        $this->path = ($id) ? 'apps/'.$id : 'apps';

        $this->setRequest();
        return $this;
    }

    public function Databases($id = null)
    {
        $this->path = ($id) ? 'dbs/'.$id : 'dbs';

        $this->setRequest();
        return $this;
    }


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


    public function Resource($Resource, $id = null)
    {
        $this->$Resource($id);

        return $this;
    }

    public function listAll()
    {
        return $this->response;
    }

    public function get()
    {
        return $this->response->data;
    }

    private function setRequest()
    {
        $this->response = $this->request($this->auth,$this->path,$this->data);
    }
}