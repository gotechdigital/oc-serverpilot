<?php namespace Awebsome\Serverpilot\Models;

use Model;

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
    protected $fillable = ['app_id','user_id','server_id','name','runtime','ssl','domains'];

    /**
     * @var array jSonables fields
     */
    protected $jsonable = ['domains','ssl'];

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