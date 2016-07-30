<?php namespace Awebsome\Serverpilot\Models;

use Model;

/**
 * Sync Model
 */
class Sync extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'awebsome_serverpilot_scheduling';

    public $timestamps = true;
    
    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];
    protected $jsonable = ['log'];

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