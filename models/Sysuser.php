<?php namespace Awebsome\Serverpilot\Models;

use Crypt;
use Model;
use ValidationException;

use Awebsome\ServerPilot\Models\Server;

use Illuminate\Contracts\Encryption\DecryptException;

/**
 * SystemUser Model
 */
class Sysuser extends Model
{
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
        'apps' => ['Awebsome\ServerPilot\Models\App', 'key' => 'api_id', 'otherKey' => 'app_api_id']
    ];
    public $belongsTo = [
        'server' => ['Awebsome\ServerPilot\Models\Server', 'key' => 'server_api_id', 'otherKey' => 'api_id']
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

    public function passwordDecrypt()
    {

        //Crypt::encrypt($value);
        try {
            return Crypt::decrypt($this->password);
        }
        catch (DecryptException $ex) {
            return null;
        }
    }
}
