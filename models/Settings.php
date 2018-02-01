<?php namespace Awebsome\Serverpilot\Models;

use Event;
use Model;
use Awebsome\ServerPilot\Models\Runtime;
/*
 * Settings Model
 */
class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'awebsome_serverpilot_settings';
    public $settingsFields = 'fields.yaml';

    /**
     * @var string The database table used by the model.
     */
    public $table = 'awebsome_serverpilot_settings';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];


    use \October\Rain\Database\Traits\Validation;

    public $rules = [
        #API ServerPilot
        'CLIENT_ID'    => 'required',
        'API_KEY'   => 'required'
    ];

    public function afterSave()
    {
        # refresh data.
        Event::fire('awebsome.serverpilot.afterSaveSettings');
    }

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
