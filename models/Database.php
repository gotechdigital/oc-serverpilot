<?php namespace Awebsome\Serverpilot\Models;

use Model;

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
    protected $fillable = ['db_id','app_id','server_id','user'];

    /**
     * @var array jSonable fields
     */
    protected $jsonable = ['user'];

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