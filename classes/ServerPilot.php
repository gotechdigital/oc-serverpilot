<?php namespace Awebsome\Serverpilot\Classes;

use Backend;
use Redirect;
use ValidationException;
use  Awebsome\Serverpilot\Classes\Api\Curl;
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
    public $data;               # data for create, update, or delete.
    public $resource;           # resource to /endpoint


    /**
     * isAuth
     * ==============================
     * check authentication
     * @return boolean is auth
     */
    public static function isAuth($failRedirect = null)
    {
        $sp = new Self;
        $response = $sp->servers();
        $code = @$response->error->code;

        if($code != 401)
            return true;
        else return false;
    }

    /**
     * Servers
     * ==============================
     * Resources Methods ServerPilot
     * @param object json response
     */
    public static function servers($id = null)
    {
        $sp = new Self;
        $sp->resource = ($id) ? __FUNCTION__ . '/'.$id : __FUNCTION__;

        return $sp->get();
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
        return $this->request($this->resource, $this->data);
    }

    # update
    # delete
}
