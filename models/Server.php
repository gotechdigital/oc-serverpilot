<?php namespace Awebsome\Serverpilot\Models;

use Model;
use Flash;
use ValidationException;

use Awebsome\Serverpilot\Classes\ServerPilot;

/**
 * Server Model
 */
class Server extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'awebsome_serverpilot_servers';

    /**
     * @var array Relations
     */
    public $hasMany = [
        'apps'      => [App::class, 'key' => 'server_api_id', 'otherKey' => 'api_id'],
        'sysusers'  => [Sysuser::class, 'key' => 'server_api_id', 'otherKey' => 'api_id'],
        'databases' => [Database::class, 'key' => 'server_api_id', 'otherKey' => 'api_id'],
    ];

    /**
     * @var boolean Flags that the model is currently being imported
     */
    public $importing;

    /**
     * Runs before the model is updated in order to sync the settings with ServerPilot
     *
     * @return void
     */
    public function beforeUpdate()
    {
        if (!$this->importing) {
            ServerPilot::servers($this->api_id)->update([
                'autoupdates'          => (bool) $this->autoupdates,
                'firewall'             => (bool) $this->firewall,
                'deny_unknown_domains' => (bool) $this->deny_unknown_domains,
            ]);
        }
    }
}
