<?php namespace Awebsome\Serverpilot\Models;

use Crypt;
use Model;
use ValidationException;

use Illuminate\Contracts\Encryption\DecryptException;

/**
 * SystemUser Model
 */
class Sysuser extends Model
{
    # use \October\Rain\Database\Traits\Purgeable;
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
    protected $fillable = ['id', 'server_id','name','password'];

    /**
     * @var array Purgeable fields
     */
    # protected $purgeable = [];

    /**
     * @var array Relations
     */
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [
        'apps' => 'Awebsome\ServerPilot\Models\App',
        'server' => 'Awebsome\ServerPilot\Models\Server'
    ];


    /**
     * @var array Validation rules
     */
    protected $rules = [
        'name' => ['required','alpha_dash', 'between:3,16'],
        'server_id' => ['required'],
        'password' => ['between:8,255']
    ];

    public function beforeCreate()
    {
        /**
         * Execute only from Create User From
         */
        /*if(post('create_system_user_form')){

            $ServerPilot = new ServerPilot;

            $SystemUser = $ServerPilot->SystemUsers()->create([
                        'serverid'=> post('SystemUser.server_id'),
                        'name'=> strtolower(post('SystemUser.name')),
                        'password'=> post('SystemUser.password')
                    ]);

            if($SystemUser->data->id)
                $this->id = $SystemUser->data->id;
            else throw new ValidationException(['error_mesage' => json_encode($SystemUser)]);
        }*/

    }

    /**
     * Set before create or save values
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Crypt::encrypt($value);
        # to decrypt use:
        # Crypt::decrypt($encryptedValue);
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
