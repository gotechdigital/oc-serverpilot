<?php namespace Awebsome\Serverpilot\Classes;

/**
 * Bridge to ServerPilot with October Database
 */
class ImportHandler
{
    /**
     * Import record.
     */
    public static function import($resource, $data)
    {
        # resource model.
        $model = $resource->model;

        # get existing
        $existing = $model::where('api_id', $data->id)->first();

        if (!$existing) {
            $model = new $model;
        } else {
            $model = $model::find($existing->id);
        }

        foreach ($resource->table as $col => $map) {
            $model->$col = Self::getValue($data, $map, $model);
        }

        $model->importing = true;
        $model->save();

        return $model;
    }

    /**
     * getValue
     * ================================================
     * @return data from api to apply in model record
     */
    public static function getValue($data, $map, $model)
    {
        # get API DATA ATTR NAME
        $key = current($map);

        # get MODEL MUTATOR METHOD
        $mutator = (count($map) >= 2) ? next($map) : null;

        $value = @$data->$key;

        if($mutator)
            $value = $model::$mutator($value);

        return $value;
    }

    /**
     * Import All
     * =============================================
     * import from list all $resource
     * Some resources do not show all the details of record in this list:
     * ex: https://api.serverpilot.io/v1/apps
     *
     * If details are required obtain one by one
     * ex: https://api.serverpilot.io/v1/apps/:api_id
     */
    public static function all($resource)
    {
        # list record data
        $records = $resource->get();

        foreach ($records->data as $data) {
            Self::import($resource, $data);
        }

        return $records;
    }

    /**
     * Import Batch
     * ====================================================
     * get all records of resource
     */
    public static function allOneToOne($resource)
    {
        # list record data
        $records = $resource->get();
        $resource = $resource->name;

        foreach ($records->data as $data) {
            ServerPilot::$resource($data->id)->import();
        }

        return $records;
    }


    public static function DeleteNonExistentResources()
    {
        // get all local resources.
        $sp = ServerPilot::instance();

        //get Resources
        $models = $sp->registerModels();
        $resources = ['servers', 'sysusers', 'apps', 'dbs' ];

        $nonExistent = [];

        foreach ($resources as $resource)
        {
            $model = $models[$resource];
            $apiRecords = @ServerPilot::$resource()->get()->data;

            if(count($apiRecords) >= 1)
            {
                $existingIds = array_column($apiRecords, 'id');
                $deleted =  $model::select('id', 'api_id')->whereNotIn('api_id', $existingIds);
                $nonExistent[$resource] = $deleted->get();

                $deleted->delete();
            }
        }

        return $nonExistent;
    }
}
