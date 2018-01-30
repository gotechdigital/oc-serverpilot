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

        if(!$existing)
            $model = new $model;
        else $model = $model::find($existing->id);

        foreach ($resource->table as $col => $value) {
            $model->$col = @$data->$value;
        }

        $model->importing = true;
        $model->save();

        return $model;
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
    public static function importBatch($resource)
    {

    }
}
