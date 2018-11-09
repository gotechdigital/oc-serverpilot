<?php namespace Awebsome\Serverpilot\Models;

use Event;
use Model;
use Awebsome\ServerPilot\Models\Runtime;

/*
 * Settings Model
 */
class Settings extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'awebsome_serverpilot_settings';
    public $settingsFields = 'fields.yaml';

    /**
     * @var string The database table used by the model.
     */
    public $table = 'awebsome_serverpilot_settings';

    /**
     * @var array The validation rules for the model
     */
    public $rules = [
        'CLIENT_ID' => 'required',
        'API_KEY'   => 'required'
    ];

    public function afterSave()
    {
        // Trigger a sync with ServerPilot
        Event::fire('awebsome.serverpilot.afterSaveSettings');
    }

    /**
     * Get the available runtime options
     *
     * @return array
     */
    public function getRuntimeOptions()
    {
        $runtimes = Runtime::orderBy('id', 'desc')->get();

        $options = [];
        foreach ($runtimes as $runtime) {
            $options[$runtime->version] = $runtime->label .' '. (($runtime->recommended) ? '':'(not recommended)');
        }

        return $options;
    }
}
