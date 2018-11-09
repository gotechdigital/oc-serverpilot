<?php namespace Awebsome\Serverpilot\Models;

use Model;
use Crypt;
use ValidationException;
use Illuminate\Contracts\Encryption\DecryptException;

use Awebsome\ServerPilot\Models\Settings as CFG;

use Awebsome\Serverpilot\Classes\ServerPilot;

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
     * @var array Attributes to be cast to JSON
     */
    protected $jsonable = ['user'];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'app'    => [App::class, 'key' => 'app_api_id', 'otherKey' => 'api_id'],
        'server' => [Server::class, 'key' => 'server_api_id', 'otherKey' => 'api_id'],
    ];

    /**
     * @var boolean Flags that the model is currently being imported
     */
    public $importing;

    /**
     * Runs before the model is created in order to sync the settings with ServerPilot
     *
     * @return void
     */
    public function beforeCreate()
    {
        if (!$this->importing) {
            $db = ServerPilot::dbs()->create([
                'appid' => $this->app_api_id,
                'name'  => $this->name,
                'user'  => [
                    'name' => $this->user['name'],
                    'password' => $this->user['password']
                ]
            ]);

            if ($db = @$db->data) {
                $db = ServerPilot::dbs($db->id)->get()->data;
                $this->api_id = $db->id;
                $this->server_api_id = $db->serverid;
                $this->password =  $this->user['password'];
            }
        }
    }

    /**
     * Runs before the model is updated in order to sync the settings with ServerPilot
     *
     * @return void
     */
    public function beforeUpdate()
    {
        if (!$this->importing && post('Database.password')) {
            ServerPilot::dbs($this->api_id)->update([
                'user' => [
                    'id' => $this->user['id'],
                    'password' => $this->passwordDecrypt(),
                ]
            ]);
        }
    }

    /**
     * Runs before the model is deleted in order to sync the settings with ServerPilot
     *
     * @return void
     */
    public function beforeDelete()
    {
        ServerPilot::dbs($this->api_id)->delete();
    }

    public function getPasswordDecryptAttribute()
    {
        return $this->passwordDecrypt();
    }

    public function getUserNameAttribute()
    {
        return @$this->user['name'];
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
