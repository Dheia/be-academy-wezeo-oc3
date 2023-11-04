<?php namespace App\Arrival\Controllers;

use App\Arrival\Models\Arrival;
use Backend;
use BackendMenu;
use Backend\Classes\Controller;
use Flash;

class Arrivals extends Controller
{
    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('App.Arrival', 'main-menu-item', );
    }

    // the function called when you click on this plugin's button on the upper backend panel
    public function index() {
        // if you dont RETURN anything here, its gonna return the index.htm file in the views directory

        $config = $this->makeConfig('$/app/arrival/models/arrival/columns.yaml');

        $config->model = new \App\Arrival\Models\Arrival;

        $widget = $this->makeWidget('Backend\Widgets\Lists', $config);

        $this->vars['listWidget'] = $widget;
    }

    public function onRedirectToUpdate() {

        // redirects the user to the update_arrival view

        return \Backend::redirect('app/arrival/arrivals/update_arrival');
    }

    // without this method, the redirect in index_onInsert would not work
    public function update_arrival() {
        $config = $this->makeConfig('$/app/arrival/models/arrival/fields.yaml');
        $config->model = new \App\Arrival\Models\Arrival;
        
        $widget = $this->makeWidget('Backend\Widgets\Form', $config);
    
        $this->vars['formWidget'] = $widget;
    }

    public function onClickUpdate() {
        
        $dataFromForm = post(); // all the form data is returned by post, basicaly the same as the superglobal $_POST
        
        //TODO: FINISH DATE AND TIME VALIDATION
        /*if
        (
            !preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{1,4}/", $dataFromForm["date"])
        ) {
            \Flash::error("Invalid date. The correct format is dd/mm/yyyy");
            return;
        }

        if
        (
            !strtotime($dataFromForm["time"]) 
            || 
            !preg_match("/(\d{1,2}):(\d{1,2})/", $dataFromForm["time"])
        ) {
            \Flash::error("Invalid time. The correct format is hh:mm");
            return;
        }*/

        $arrival = new Arrival;
        $arrival->name = $dataFromForm['name'];
        $arrival->date = $dataFromForm['date'];
        $arrival->time = $dataFromForm['time'];
        $arrival->message = $dataFromForm['message'];
        $arrival->save();

        \Flash::success("Added " . $dataFromForm['name']);
    }
}