<?php namespace Awebsome\Serverpilot\Models;

use Model;

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
    protected $fillable = ['id','name','autoupdates','firewall','lastaddress','datecreated','lastconn','created_at'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
                'systemusers'   => ['Awebsome\Serverpilot\Models\SystemUser','key' => 'server_id','otherKey' => 'id'],
                'apps'          => ['Awebsome\Serverpilot\Models\App','key' => 'server_id','otherKey' => 'id'],
                'databases'     => ['Awebsome\Serverpilot\Models\Database','key' => 'server_id','otherKey' => 'id'],
            ];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}
