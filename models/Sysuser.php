<?php namespace Awebsome\Serverpilot\Models;

use Log;
use Crypt;
use Model;

use Awebsome\ServerPilot\Models\Server;
use Awebsome\ServerPilot\Models\Settings as CFG;

use Awebsome\ServerPilot\Classes\ServerPilot;
use Illuminate\Contracts\Encryption\DecryptException;

/**
 * SystemUser Model
 */
class Sysuser extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'awebsome_serverpilot_sysusers';

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
    public $hasMany = [
        'apps' => ['Awebsome\Serverpilot\Models\App','key' => 'sysuser_api_id','otherKey' => 'api_id'],
    ];
    public $belongsTo = [
        'server' => ['Awebsome\ServerPilot\Models\Server', 'key' => 'server_api_id', 'otherKey' => 'api_id']
    ];

    /**
     * @var array Validation rules
     */
    protected $rules = [
        'user'=> 'alpha_num|between: 3, 32',
    ];

    /**
     * check if it's an import
     * @param boolean
     */
    public $importing;

    public function beforeCreate()
    {
        if(!$this->importing)
        {
            $sysuser = ServerPilot::sysusers()->create([
                'serverid' => post('Sysuser.server_api_id'),
                'name' => $this->name,
                'password' => post('Sysuser.password')
            ]);

            if($sysuser = @$sysuser->data)
            {
                $this->api_id = $sysuser->id;
                $this->server_api_id = $sysuser->serverid;
                # Log::info('creado...'. json_encode($sysuser));
            }
        }
    }

    public function beforeUpdate()
    {
        if(!$this->importing && post('Sysuser.password'))
        {
            ServerPilot::sysusers($this->api_id)->update([
                'password' => $this->passwordDecrypt()
            ]);
            # Log::info('sysuser Updateado...');
        }
    }

    /**
     * Set USER.
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
            if(CFG::get('show_sysuser_password'))
                $password = $this->passwordDecrypt();
            else $password = '....Encrypted....';

        } else $password = '... Unknown ...';

        return $password;
    }

    /**
     * Set USER.
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
    }


    public function passwordDecrypt()
    {
        # to decrypt use:
        # Crypt::decrypt($encryptedValue);
        try {
            return Crypt::decrypt($this->password);
        }
        catch (DecryptException $ex) {
            return null;
        }
    }

    public function getServersOptions()
    {
        $servers = Server::all();
        $options = [];

        foreach ($servers as $server) {
            $options[$server->api_id] = $server->name;
        }

        return $options;
    }
}
