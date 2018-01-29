<?php namespace Awebsome\Serverpilot\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Actions Back-end Controller
 */
class Actions extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Awebsome.Serverpilot', 'serverpilot', 'actions');
    }
}
