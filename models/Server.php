<?php namespace Awebsome\Serverpilot\Models;

use Model;
use ValidationException;

use Awebsome\Serverpilot\Classes\ServerPilot;

/**
 * Server Model
 */
class Server extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'awebsome_serverpilot_servers';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['*'];

    /**
     * @var array Relations
     */
    public $hasOne = [];

    public $hasMany = [
                'sysusers'      => ['Awebsome\Serverpilot\Models\Sysuser','key' => 'server_api_id','otherKey' => 'api_id'],
                'databases'     => ['Awebsome\Serverpilot\Models\Database','key' => 'server_api_id','otherKey' => 'api_id'],
                'apps'          => ['Awebsome\Serverpilot\Models\App','key' => 'server_api_id','otherKey' => 'api_id']
            ];

    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    /* public function beforeUpdate()
    {
        $ServerPilot = new ServerPilot;

        $ServerPilot->Servers($this->id)->update([
            'autoupdates'   => ($this->autoupdates) ? true : false,
            'firewall'      => ($this->firewall) ? true : false,
            'deny_unknown_domains'   => ($this->deny_unknown_domains) ? true : false,
        ]);
    } */
}
