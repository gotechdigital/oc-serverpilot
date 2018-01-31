<?php namespace Awebsome\Serverpilot\Models;

use Log;
use Model;
use Crypt;
use Flash;
use ValidationException;
use Illuminate\Contracts\Encryption\DecryptException;

use Awebsome\ServerPilot\Models\Settings as CFG;

use Awebsome\Serverpilot\Classes\ServerPilot;

/**
 * Database Model
 */
class Database extends Model
{
    //use \October\Rain\Database\Traits\Purgeable;
    use \October\Rain\Database\Traits\Validation;

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
    protected $fillable = ['*'];

    /**
     * @var array jSonable fields
     */
    protected $jsonable = ['user'];


    /**
    * @var array Validation rules
    */
    protected $rules = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'app'       => ['Awebsome\Serverpilot\Models\App', 'key' => 'app_api_id', 'otherKey' => 'api_id'],
        'server'       => ['Awebsome\Serverpilot\Models\Server', 'key' => 'server_api_id', 'otherKey' => 'api_id'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];


    /**
     * check if it's an import
     * @param boolean
     */
    public $importing;

    public function beforeCreate()
    {
        if(!$this->importing)
        {
            $db = ServerPilot::dbs()->create([
                'appid'=> $this->app_api_id,
                'name'=> $this->name,
                'user'=> [
                        'name' => $this->user['name'],
                        'password' => $this->user['password']
                    ]
            ]);

            if($db = @$db->data)
            {
                $db = ServerPilot::dbs($db->id)->get()->data;
                $this->api_id = $db->id;
                $this->server_api_id = $db->serverid;
                $this->password =  $this->user['password'];
            }
        }
    }

    public function beforeUpdate()
    {
        if(!$this->importing && post('Database.password'))
        {
            ServerPilot::dbs($this->api_id)->update([
                'user' => [
                    'id' => $this->user['id'],
                    'password' => $this->passwordDecrypt(),
                ]
            ]);
        }
    }

    public function beforeDelete()
    {
        ServerPilot::dbs($this->api_id)->delete();
    }

    /**
     * Set Database.
     */
    public function setPasswordAttribute($pass)
    {
        if($pass)
            $pass = Crypt::encrypt($pass);
        else if ($this->password)
        {
            $pass = $this->password;
        }

        $this->attributes['password'] = $pass;
        # to decrypt use:
        # Crypt::decrypt($encryptedValue);
    }

    public function getVisiblePasswordAttribute()
    {
        if($this->password)
        {
            if(CFG::get('show_dbs_password'))
                $password = $this->passwordDecrypt();
            else $password = '....Encrypted....';

        } else $password = '... Unknown ...';

        return $password;
    }

    public function passwordDecrypt()
    {
        try {
            return Crypt::decrypt($this->password);
        }
        catch (DecryptException $ex) {
            return null;
        }
    }
}
