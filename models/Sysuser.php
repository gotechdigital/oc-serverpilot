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

    public function beforeSave()
    {
        if(!$this->importing && post('Sysuser.password'))
        {
            ServerPilot::sysusers($this->api_id)->update([
                'password' => $this->passwordDecrypt()
            ]);
            // Log::info('sysuser Updateado...');
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
    public function setUserAttribute($user)
    {
        if(!$this->user)
            $user = strtolower($user);
        else $user = $this->user;

        $this->attribute['user'] = $user;
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
}
