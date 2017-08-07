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
    public $timestamps = true;

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['id','name','autoupdates','firewall','lastaddress','datecreated','lastconn','created_at','deny_unknown_domains'];

    /**
     * @var array Relations
     */
    public $hasOne = [];

    public $hasMany = [
                'systemusers'   => ['Awebsome\Serverpilot\Models\SystemUser','key' => 'server_id','otherKey' => 'id'],
                'databases'     => ['Awebsome\Serverpilot\Models\Database','key' => 'server_id','otherKey' => 'id'],
                'apps'          => ['Awebsome\Serverpilot\Models\App','key' => 'server_id','otherKey' => 'id']
            ];

    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function beforeUpdate()
    {
        $ServerPilot = new ServerPilot;

        $ServerPilot->Servers($this->id)->update([
            'autoupdates'   => ($this->autoupdates) ? true : false,
            'firewall'      => ($this->firewall) ? true : false,
            'deny_unknown_domains'   => ($this->deny_unknown_domains) ? true : false,
        ]);
    }
}
