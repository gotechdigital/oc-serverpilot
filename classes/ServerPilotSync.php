<?php namespace Awebsome\Serverpilot\Classes;

use Flash;

use  Awebsome\Serverpilot\Models\Server;
use  Awebsome\Serverpilot\Models\Database;
use  Awebsome\Serverpilot\Models\SystemUser;
use  Awebsome\Serverpilot\Models\App;
use  Awebsome\Serverpilot\Models\Sync;

use  Awebsome\Serverpilot\Classes\ServerPilot;

/**
 * Bridge to ServerPilot with October Database
 */
class ServerPilotSync
{
    public $ServerPilot;
    public $syncResource;
    public $idRowName;
    public $scheduleName;
    public $modelPath;

    public $syncLog;
    public $disabledLog;
    public $Response;

    function __construct()
    {
        /**
         * Authentication with
         * @var ServerPilot
         */
        $this->ServerPilot = new ServerPilot;
        $this->syncLog = [];
    }

    /**
     * Servers Resource
     * ===========================
     * Configs
     */
    public function Servers()
    {
        $this->syncResource     = 'Servers';
        $this->idRowName        = 'id';
        $this->scheduleName     = 'sync_servers';
        $this->modelPath        = 'Awebsome\Serverpilot\Models\Server';

        return $this;
    }

    /**
     * Apps Resource
     * ===========================
     * Configs
     */
    public function Apps()
    {
        $this->syncResource     = 'Apps';
        $this->idRowName        = 'id';
        $this->scheduleName     = 'sync_apps';
        $this->modelPath        = 'Awebsome\Serverpilot\Models\App';

        return $this;
    }

    /**
     * SystemUsers Resource
     * ===========================
     * Configs
     */
    public function SystemUsers()
    {
        $this->syncResource     = 'SystemUsers';
        $this->idRowName        = 'id';
        $this->scheduleName     = 'sync_system_users';
        $this->modelPath        = 'Awebsome\Serverpilot\Models\SystemUser';

        return $this;
    }

    /**
     * Databases Resource
     * ===========================
     * Configs
     */
    public function Databases()
    {
        $this->syncResource     = 'Databases';
        $this->idRowName        = 'id';
        $this->scheduleName     = 'sync_databases';
        $this->modelPath        = 'Awebsome\Serverpilot\Models\Database';

        return $this;
    }


    /**
     * Now
     * ========================
     * Execution sync,
     * get data form ServerPilot
     * and put data in the database
     *
     * @return array Resources petition
     */
    public function now()
    {
        # Data Response from ServerPilot
        $Resources = $this->ServerPilot->Resource($this->syncResource)->listAll()->data;

        # SyncTo Model path ex: 'Awebsome\Serverpilot\Models\Server'
        $ResourceModel = $this->modelPath;



        # Create or Update resource
        if(count($Resources) > 0)
        {
            foreach ($Resources as $resource => $value) {

                # Mapping data
                $data = $this->dataMapping($value);

                $Resource = $ResourceModel::find($value->id);
                if($Resource)
                {
                    # Update Resource
                    $Resource->update($data);
                        $this->addSyncLog($this->scheduleName, $this->syncResource.': '.$value->name.' updated');
                }
                else {
                        # Create Resource
                        $ResourceModel::create($data);
                            $this->addSyncLog($this->scheduleName, $this->syncResource.': '.$value->name.' Added');
                    }
            }
        }

        # Resources that have been deleted in ServerPilot
        $resourceDeleted = $ResourceModel::whereNotIn($this->idRowName, array_column(array_map('get_object_vars', $Resources), 'id'))->delete();

        # Delete resources records
        $this->addSyncLog($this->scheduleName, $this->syncResource.' Deleted: '.json_encode($resourceDeleted));

        return $this;
    }

    /**
     * dataMapping
     * =========================
     * Mapping data for resources queries.
     *
     * @param  array $value data
     * @return array        data
     */
    public function dataMapping($value)
    {
        switch ($this->syncResource) {
            case 'Servers':
                    $data = [
                            'id'            => $value->id,
                            'name'          => $value->name,
                            'autoupdates'   => $value->autoupdates,
                            'firewall'      => $value->firewall,
                            'lastaddress'   => $value->lastaddress,
                            'datecreated'   => $value->datecreated,
                            'lastconn'      => $value->lastconn,
                            'created_at'    => $value->datecreated
                        ];
                break;

            case 'SystemUsers':
                    $data = [
                            'id'            => $value->id,
                            'server_id'     => $value->serverid,
                            'name'          => $value->name
                        ];
                break;

            case 'Databases':
                    $data = [
                            'id'            => $value->id,
                            'app_id'        => $value->appid,
                            'server_id'     => $value->serverid,
                            'user'          => $value->user,
                            'name'          => $value->name
                        ];
                break;

            case 'Apps':
                    $data = [
                        'id'                => $value->id,
                        'user_id'           => $value->sysuserid,
                        'server_id'         => $value->serverid,
                        'name'              => $value->name,
                        'runtime'           => $value->runtime,
                        'ssl'               => $value->ssl,
                        'created_at'        => $value->datecreated,
                        'domains'           => $this->getDomains($value->domains)
                    ];
                break;
        }

        return $data;
    }

    /**
     * getDomains
     * ======================================
     * reformat array to repeater field format
     * @param  array  $domains
     * @return array
     */
    public function getDomains($domains)
    {
        foreach ($domains as $key => $value)
        {
            $allDomains[]['domain'] = $value;
        }

        return $allDomains;
    }


    /**
     * putUpdateResource
     * ======================================
     * Put sync schelude log
     * Update
     * @param  string $resource method
     * @param  array $data     data
     * @param  array $response curl response
     */
    public function putUpdateResource($resource, $data, $response)
    {

        $schedule    = 'update_resource_'.strtolower($resource);
        $log         = 'petition: '.json_encode($data).' response:'.json_encode($response);

        $this->addSyncLog($schedule, $log)->log($schedule);

        # $this->all();
    }

    /**
     * putCreateResource
     * ======================================
     * Put sync schelude log
     * Update
     * @param  string $resource method
     * @param  array $data     data
     * @param  array $response curl response
     */
    public function putCreateResource($resource, $data, $response)
    {

        $schedule    = 'create_resource_'.strtolower($resource);
        $log         = 'petition: '.json_encode($data).' response:'.json_encode($response);

        $this->addSyncLog($schedule, $log)->log($schedule);

        # $this->all();
    }

    /**
     * Sync All Resources
     */
    public function All()
    {
        $this->Servers()->now();
        $this->SystemUsers()->now();
        $this->Databases()->now();
        $this->Apps()->now();

        return $this;
    }

     public function addSyncLog($name, $log = null)
    {
        $this->syncLog[] = [
                        'name' => $name,
                        'log'  => $log
                    ];

        return $this;
    }

    /**
     * Schedules Logs.
     *
     * @param string $schedule      ~# schedule name
     * @param string $log           ~# Log or description
     */
    public function Schedule($name)
    {
        if(!$this->disabledLog)
        {
            $Model = new Sync;
            $Model->schedule    = $name;
            $Model->log         = $this->syncLog;
            $Model->save();

            return $Model;
        }
    }

    public function testRequest()
    {
       $request = $this->ServerPilot->Resource($this->syncResource)->listAll()->data;
       return '<pre>'.json_encode($request, JSON_PRETTY_PRINT).'</pre>';
    }

    public function log($name = null)
    {
        $this->Schedule($name);
    }

}
