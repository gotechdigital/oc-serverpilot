<?php namespace Awebsome\Serverpilot\Models;

use Model;

/**
 * SystemUser Model
 */
class SystemUser extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'awebsome_serverpilot_system_users';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['user_id', 'server_id','name'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}