<?php namespace Awebsome\Serverpilot\Models;

use Model;
use Flash;
use ValidationException;
use Awebsome\Serverpilot\Models\Settings;
use Awebsome\Serverpilot\Models\SystemUser;
use Awebsome\Serverpilot\Models\Database;

use Awebsome\Serverpilot\Classes\Atom;
use Awebsome\Serverpilot\Classes\Sublime;
use Awebsome\Serverpilot\Classes\ServerPilot;
use Awebsome\Serverpilot\Classes\ServerPilotSync;

/**
 * App Model
 */
class App extends Model
{
    //use \October\Rain\Database\Traits\Purgeable;

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
     * @var array Purgeable fields
     */
    //protected $purgeable = ['server_name', 'auto_ssl', 'force_ssl'];

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
}
