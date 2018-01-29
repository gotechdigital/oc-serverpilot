<?php namespace Awebsome\Serverpilot\Models;

use Model;
use ValidationException;
use Awebsome\Serverpilot\Models\App;

use Awebsome\Serverpilot\Classes\ServerPilot;

/**
 * Database Model
 */
class Database extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'awebsome_serverpilot_databases';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];


    /**
     * @var array Fillable fields
     */
    protected $fillable = ['*'];

    /**
     * @var array jSonable fields
     */
    protected $jsonable = ['user'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'app'       => ['Awebsome\Serverpilot\Models\App', 'key' => 'app_api_id', 'otherKey' => 'api_id'],
        'server'       => ['Awebsome\Serverpilot\Models\Server', 'key' => 'server_api_id', 'otherKey' => 'api_id'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];


    /**
     * @var array Validation rules
     */
    protected $rules = [];

    public function beforeCreate()
    {

        /**
         * Execute only from Create Database From
         */
        /*if(post('create_database_form') || post('_relation_mode')){

            $ServerPilot = new ServerPilot;

            $Database = $ServerPilot->Databases()
                    ->create([
                        'appid'=> $this->app_id,
                        'name'=> $this->name,
                        'user'=> [
                                'name' => $this->user['name'],
                                'password' => $this->user['password']
                            ]
                    ]);

            if($Database->data->id && $Database->data->serverid)
            {
                $this->id = $Database->data->id;
                $this->server_id = $Database->data->serverid;
                $this->user = $Database->data->user;
            }else throw new ValidationException(['error_mesage' => json_encode($Database)]);
        }*/
    }

    public function afterDelete()
    {
        /*$ServerPilot = new ServerPilot;
        $ServerPilot->Databases($this->id)->delete();*/
    }

    /**
     * [getAppsList description]
     * @return [type] [description]
     */
    public function getAppsList()
    {
        /*$Apps = App::get();
        $options = [];
        foreach ($Apps as $key => $value) {
            $options[$value->id] = $value->name;
        }

        return $options;*/
    }
}
