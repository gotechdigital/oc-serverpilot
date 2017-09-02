<?php namespace Awebsome\Serverpilot\Models;

use Model;
use Flash;
use ValidationException;
use Awebsome\Serverpilot\Models\Settings;
use Awebsome\Serverpilot\Models\SystemUser;
use Awebsome\Serverpilot\Models\Database;

use Awebsome\Serverpilot\Classes\Atom;
use Awebsome\Serverpilot\Classes\Sublime;
use Awebsome\Serverpilot\Classes\ServerPilot;
use Awebsome\Serverpilot\Classes\ServerPilotSync;

/**
 * App Model
 */
class App extends Model
{
    use \October\Rain\Database\Traits\Purgeable;

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
    protected $fillable = ['id','user_id','server_id','name','runtime','ssl','domains','all_domains','created_at'];

    /**
     * @var array jSonables fields
     */
    protected $jsonable = ['domains','ssl','all_domains'];

    /**
     * @var array Purgeable fields
     */
    protected $purgeable = ['server_name', 'auto_ssl', 'force_ssl'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
                'databases'     => ['Awebsome\Serverpilot\Models\Database','key' => 'app_id','otherKey' => 'id'],
            ];
    public $belongsTo = [
                'systemuser'   => ['Awebsome\Serverpilot\Models\SystemUser','key' => 'user_id'],
                'server'     => ['Awebsome\Serverpilot\Models\Server','key' => 'server_id'],
            ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

     use \October\Rain\Database\Traits\Validation;

    /**
     * @var array Validation rules
     */
    protected $rules = [
        'user_id'   => ['required'],
        'runtime'   => ['required'],
        'name'      => ['required', 'between:3,16','alpha_dash'],
        'domains'   => ['required']
    ];

    public function beforeCreate()
    {

        if(post('create_app_form'))
        {
            $ServerPilot = new ServerPilot;

            $domains = $this->getFirstDomains($this->domains);

            $App = $ServerPilot->Apps()->create([
                        'name'      => $this->name,
                        'sysuserid' => $this->user_id,
                        'runtime'   => $this->runtime,
                        'domains'   => $domains,
                    ]);

            if($App->data->id && $App->data->serverid)
            {
                $this->id = $App->data->id;
                $this->server_id = $App->data->serverid;
                $this->ssl = $App->data->ssl;
                $this->domains = $this->setDomains($App->data->domains);

                //set AtomConfig
                if(empty($this->atom_config)){
                    $this->atom_config = $this->getAtomConfig();
                }
                //set SublimeConfig
                if(empty($this->sublime_config)){
                    $this->sublime_config = $this->getSublimeConfig();
                }

            }else throw new ValidationException(['error_mesage' => json_encode($App)]);
        }
    }

    public function beforeUpdate()
    {
        if(post('save_app_update'))
        {
            $ServerPilot = new ServerPilot;

            $App = $ServerPilot->Apps($this->id)->update([
                            #'name'      => $this->name,
                            #'sysuserid' => $this->user_id,
                            'runtime'   => $this->runtime,
                            'domains'   => array_column($this->domains, 'domain')
                        ]);

            if($App->data->id && $App->data->serverid)
            {
                $this->domains = $this->setDomains($App->data->domains);

            }else throw new ValidationException(['error_mesage' => json_encode($App)]);
        }

        //set AtomConfig
        if(empty($this->atom_config)){
            $this->atom_config = $this->getAtomConfig();
        }

        //set SublimeConfig
        if(empty($this->sublime_config)){
            $this->sublime_config = $this->getSublimeConfig();
        }
    }

    public function beforeDelete()
    {
        $ServerPilot = new ServerPilot;
        $ServerPilot->Apps($this->id)->delete();
    }

    public function afterCreate()
    {
        $Sync = new ServerPilotSync;
        $Sync->Apps()->now();
    }

    public function afterDelete()
    {
        $Sync = new ServerPilotSync;
        $Sync->Databases()->now();
    }

    /**
     * getFirstDomains
     * =====================================
     * Domains for create app
     * @param  string $domains  Main-domain
     * @return array            domains to server pilot
     */
    public function getFirstDomains($domain)
    {
        $domains[] = $domain;
        $domains[] = 'www.'.$domain;

        $prevDomain = $this->getPrevDomain();

        if($prevDomain)
            $domains[] = $prevDomain;

        return $domains;
    }

    /**
     * getDomains
     * ======================================
     * reformat array to repeater field
     * @param  array  $domains
     * @return array
     */
    public function setDomains($domains)
    {
        foreach ($domains as $key => $value)
        {
            $allDomains[]['domain'] = $value;
        }

        return $allDomains;
    }

    /**
     * getPrevDomain
     * ==============================
     *
     */
    public function getPrevDomain()
    {
        $domain = Settings::get('app_prev_domain');

        if(!empty($domain) && strpos($domain, "{APP_NAME}"))
            return str_replace('{APP_NAME}', $this->name, $domain);
    }

    /**
     * getServerList
     * SystemUsers Listing for "create form"
     * @return array
     */
    public function getSystemUserList()
    {
        $SystemUsers = SystemUser::get();
        $options = [];
        foreach ($SystemUsers as $key => $value) {
            $options[$value->id] = $value->name.' :: '.$value->server->name;
        }

        return $options;
    }

    /**
     * asume the first domain with the main (default)
     */
    public function getMainDomain()
    {
        return current(current($this->domains));
    }

    /**
     * get Atom Config data
     */
    public function getAtomConfig()
    {
        $config = Atom::config([
                'host' => $this->getMainDomain(),
                'user' => $this->systemuser->name,
                'pass' => $this->systemuser->passwordDecrypt(),
                'app'  => $this->name
            ]);

        return json_encode($config, JSON_PRETTY_PRINT);
    }

    /**
     * get Sublime Config data
     */
    public function getSublimeConfig()
    {
        $config = Sublime::config([
                'host' => $this->getMainDomain(),
                'user' => $this->systemuser->name,
                'password' => $this->systemuser->passwordDecrypt(),
                'app'  => $this->name
            ]);

        return json_encode($config, JSON_PRETTY_PRINT);
    }

    public function getServerNameAttribute()
    {
        return $this->server->name;
    }

    public function getAutoSslAttribute()
    {
        return $this->ssl['auto'];
    }

    public function getForceSslAttribute()
    {
        return $this->ssl['force'];
    }
}
