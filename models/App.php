<?php namespace Awebsome\Serverpilot\Models;

use Model;

use Awebsome\Serverpilot\Models\Sysuser;
use Awebsome\Serverpilot\Classes\ServerPilot;

/**
 * App Model
 */
class App extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'awebsome_serverpilot_apps';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['*'];

    /**
     * @var array jSonables fields
     */
    protected $jsonable = ['autossl','ssl', 'domains'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'databases' => ['Awebsome\Serverpilot\Models\Database','key' => 'app_api_id', 'otherKey' => 'api_id'],
    ];
    public $belongsTo = [
        'server'   => ['Awebsome\Serverpilot\Models\Server','key' => 'server_api_id', 'otherKey' => 'api_id'],
        'sysuser'    => ['Awebsome\Serverpilot\Models\Sysuser','key' => 'sysuser_api_id', 'otherKey' => 'api_id'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    /**
     * get Sysuser Options
     */
     public function getSysuserOptions()
     {
        $users = Sysuser::all();
        $options = [];

        foreach ($users as $user) {
            $options[$user->api_id] = $user->name;
        }

        return $options;
     }
}
