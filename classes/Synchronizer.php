<?php namespace Awebsome\Serverpilot\Classes;

use Awebsome\Serverpilot\Classes\ServerPilot;

/**
 * Bridge to ServerPilot with October Database
 */
class Synchronizer
{
    // import one by API ID
    public static function import($resource)
    {
        $resource_name  = $resource->resource;
        $resource_model = $resource->model;
        $resource_data  = $resource->get()->data;

        $model = null;

        if($resource->id)
        {

            $data = $resource_data;

            $model = $resource_model::where('api_id', $data->id)->first();

            if(!$model)
            {
                $model = new $resource_model;
            }else $model = $model->find($model->id);

            foreach (Self::getColumns()->$resource_name as $col => $value) {
                $model->$col = $data->$value;
            }

            $model->importing = true;
            $model->save();

        }

        return $model;
    }

    // import ALL
    public static function importAll($resource)
    {
        $resource_name = $resource->resource;
        $resources = $resource->get()->data;

        if(count($resources) >= 1)
        {
            foreach ($resources as $resource) {
                ServerPilot::$resource_name($resource->id)->import();
            }
        }

        return $resources;
    }

    /**
     * dataMapping
     * =========================
     * Mapping data for resources queries.
     *
     * @param  array $value data
     * @return array        data
     */
    public static function getColumns()
    {
        $map = [
            'servers' => [
                'api_id'                => 'id',
                'name'                  => 'name',
                'autoupdates'           => 'autoupdates',
                'firewall'              => 'firewall',
                'lastaddress'           => 'lastaddress',
                'datecreated'           => 'datecreated',
                'lastconn'              => 'lastconn',
                'created_at'            => 'datecreated',
                'deny_unknown_domains'  => 'deny_unknown_domains'
            ],
            'sysusers' => [
                'api_id'                => 'id',
                'server_api_id'         => 'serverid',
                'name'                  => 'name'
            ],

            'dbs' => [
                'api_id'            => 'id',
                'app_api_id'        => 'appid',
                'server_api_id'     => 'serverid',
                'name'              => 'name',
                'user'              => 'user'
            ],

            'apps' => [
                'api_id'            => 'id',
                'sysuser_api_id'    => 'sysuserid',
                'server_api_id'     => 'serverid',
                'name'              => 'name',
                'runtime'           => 'runtime',
                'ssl'               => 'ssl',
                'autossl'           => 'autossl',
                'domains'           => 'domains',
                'datecreated'       => 'datecreated'
            ],

            'actions' => [
                'api_id'            => 'id',
                'server_api_id'     => 'serverid',
                'status'            => 'status',
                'datecreated'       => 'datecreated'
            ]
        ];

        return json_decode(json_encode($map));
    }
}
