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
    protected $fillable = ['id','app_id','server_id','user','name'];

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
        'app'       => ['Awebsome\Serverpilot\Models\App'],
        'server'    => ['Awebsome\Serverpilot\Models\Server']
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    use \October\Rain\Database\Traits\Validation;

    /**
     * @var array Validation rules
     */
    protected $rules = [
        'app_id' => ['required'],
        'name' => ['required','alpha_dash','between:3,16'],
        'user' => ['required']
    ];

    public function beforeCreate()
    {

        /**
         * Execute only from Create Database From
         */
        if(post('create_database_form') || post('_relation_mode')){

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
        }
    }

    public function afterDelete()
    {
        $ServerPilot = new ServerPilot;
        $ServerPilot->Databases($this->id)->delete();
    }

    /**
     * [getAppsList description]
     * @return [type] [description]
     */
    public function getAppsList()
    {
        $Apps = App::get();
        $options = [];
        foreach ($Apps as $key => $value) {
            $options[$value->id] = $value->name;
        }

        return $options;
    }
}
