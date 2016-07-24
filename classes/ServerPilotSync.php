<?php namespace Awebsome\Serverpilot\Classes;

use Flash;

use  Awebsome\Serverpilot\Models\Server;
use  Awebsome\Serverpilot\Models\Database;
use  Awebsome\Serverpilot\Models\SystemUser;
use  Awebsome\Serverpilot\Models\App;
use  Awebsome\Serverpilot\Models\Sync;
use  Awebsome\Serverpilot\Models\Settings;

use  Awebsome\Serverpilot\Classes\ServerPilot;

class ServerPilotSync
{
    public $ServerPilot;
    public $syncResource;
    public $idRowName;
    public $scheduleName;
    public $modelPath;

    function __construct()
    {
        /**
         * Authentication with
         * @var ServerPilot
         */
        $this->ServerPilot = new ServerPilot(Settings::get('CLIENT_ID'), Settings::get('API_KEY'));
    }

    /**
     * Servers Resource
     * ===========================
     * Configs
     */
    public function Servers()
    {
        $this->syncResource     = 'Servers';
        $this->idRowName        = 'server_id';
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
        $this->idRowName        = 'app_id';
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
        $this->idRowName        = 'user_id';
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
        $this->idRowName        = 'db_id';
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

        # Resources deleted on ServerPilot.
        $resourceDeleted = $ResourceModel::whereNotIn($this->idRowName, array_column(array_map('get_object_vars', $Resources), 'id'));
        
        # Delete resources records
        if($resourceDeleted->first())
        {
            $this->schedule($this->scheduleName, 'Deleted Resources: '.json_encode(array_column($resourceDeleted->get()->toArray(), 'name')));
            $resourceDeleted->delete();
        }

        # Create or Update resource
        if(count($Resources) > 0)
        {
            foreach ($Resources as $resource => $value) {
                
                # Mapping data
                $data = $this->dataMapping($value);

                $resource = $ResourceModel::where($this->idRowName, $value->id)->first();
                if($resource)
                {
                    # Update Resource
                    $ResourceModel::find($resource->id)->update($data);
                        $this->schedule($this->scheduleName, $this->syncResource.': '.$value->name.' updated');
                }
                else {
                        # Create Resource
                        $ResourceModel::create($data);
                            $this->schedule($this->scheduleName, $this->syncResource.': '.$value->name.' Added');
                    }
            }
        }

        return $Resources;
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
                            'server_id'     => $value->id,
                            'name'          => $value->name,
                            'autoupdates'   => $value->autoupdates,
                            'firewall'      => $value->firewall,
                            'lastaddress'   => $value->lastaddress,
                            'datecreated'   => $value->datecreated,
                            'lastconn'      => $value->lastconn,
                        ];
                break;

            case 'SystemUsers':
                    $data = [
                            'user_id'     => $value->id,
                            'server_id'   => $value->serverid,
                            'name'        => $value->name
                        ];
                break;

            case 'Databases':
                    $data = [
                            'db_id'     => $value->id,
                            'app_id'    => $value->appid,
                            'server_id' => $value->serverid,
                            'user'      => $value->user,
                            'name'      => $value->name
                        ];
                break;

            case 'Apps':
                    $data = [
                        'app_id'     => $value->id,
                        'user_id'    => $value->sysuserid,
                        'server_id'  => $value->serverid,
                        'name'       => $value->name,
                        'runtime'    => $value->runtime,
                        'ssl'        => $value->ssl,
                        'domains'    => $value->domains
                    ];  
                break;
        }

        return $data;
    }


    /**
     * Schedules Logs.
     * 
     * @param string $schedule      ~# schedule name
     * @param string $log           ~# Log or description
     */
    public function Schedule($schedule = null, $log = null)
    {
        $Model = new Sync;
        $Model->schedule    = $schedule;
        $Model->log         = $log;
        $Model->save();

        return $Model;
    }


    /**
     * Sync All Resources
     */
    public function All()
    {
        $this->Servers()->now();
        $this->SystemUsers()->now();
        $this->Apps()->now();
        $this->Databases()->now();

        Flash::success('Sync successful');
    }
}