<?php namespace Awebsome\Serverpilot\Models;

use Crypt;
use Model;
use Flash;
use Request;
use ValidationException;

use Awebsome\Serverpilot\Models\Server;
use Awebsome\Serverpilot\Models\Settings;
use Awebsome\Serverpilot\Models\Sync;
use Awebsome\Serverpilot\Classes\ServerPilot;

use Illuminate\Contracts\Encryption\DecryptException;
/**
 * SystemUser Model
 */
class SystemUser extends Model
{
    # use \October\Rain\Database\Traits\Purgeable;
    use \October\Rain\Database\Traits\Validation;

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
    protected $fillable = ['id', 'server_id','name','password'];

    /**
     * @var array Purgeable fields
     */
    # protected $purgeable = [];

    /**
     * @var array Relations
     */
    public $hasMany = ['apps' => ['Awebsome\Serverpilot\Models\App', 'key'=> 'user_id']];
    public $belongsTo = ['server' => ['Awebsome\Serverpilot\Models\Server', 'key' => 'server_id']];


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
        if(post('create_system_user_form')){

            $ServerPilot = new ServerPilot;

            $SystemUser = $ServerPilot->SystemUsers()->create([
                        'serverid'=> post('SystemUser.server_id'),
                        'name'=> strtolower(post('SystemUser.name')),
                        'password'=> post('SystemUser.password')
                    ]);

            if($SystemUser->data->id)
                $this->id = $SystemUser->data->id;
            else throw new ValidationException(['error_mesage' => json_encode($SystemUser)]);
        }

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

    /**
     * getServerList
     * Servers Listing for "create form"
     * @return array
     */
    public function getServerList()
    {
        $Servers = Server::get();
        $options = [];
        foreach ($Servers as $key => $value) {
            $options[$value->id] = $value->name;
        }

        return $options;
    }
}
