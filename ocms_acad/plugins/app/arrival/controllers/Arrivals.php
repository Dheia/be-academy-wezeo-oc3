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

    // Shows the list of all arrivals
    public function index() 
    {
        $config = $this->makeConfig('$/app/arrival/models/arrival/columns.yaml');
        $config->model = new \App\Arrival\Models\Arrival;
        $widget = $this->makeWidget('Backend\Widgets\Lists', $config);
        $widget->bindToController();
        $widget->recordUrl = 'app/arrival/arrivals/updatearrival/:id';
        $this->vars['listWidget'] = $widget;
    }

    // Called upon clicking on the "Insert new student" button on the index page
    public function onRedirectToInsert() 
    {
        return \Backend::redirect('app/arrival/arrivals/insertarrival');
    }

    // Shows a form for inserting a new arrival
    public function insertArrival()
    {
        $config = $this->makeConfig('$/app/arrival/models/arrival/fields.yaml');
        $config->model = new \App\Arrival\Models\Arrival;
        $widget = $this->makeWidget('Backend\Widgets\Form', $config);
        $this->vars['formWidget'] = $widget;
    }

    // Shows a form for updating an existing arrival
    public function updateArrival($id) 
    {    
        $config = $this->makeConfig('$/app/arrival/models/arrival/fields.yaml');
        $config->model = \App\Arrival\Models\Arrival::find($id);
        $widget = $this->makeWidget('Backend\Widgets\Form', $config);
        $this->vars['formWidget'] = $widget;
    }

    // Called by the "Insert" button when viewing the insertArrival page
    public function onClickInsert()
    {    
        $dataFromForm = post(); // all the form data is returned by post, basicaly the same as the superglobal $_POST

        $arrival = new Arrival;
        $arrival->name = $dataFromForm['name'];
        $arrival->date = $dataFromForm['date'];
        $arrival->time = $dataFromForm['time'];
        $arrival->message = $dataFromForm['message'];
        $arrival->save();

        \Flash::success("Added " . $dataFromForm['name']);
    }

    // Called by the "Update" button when viewing the updateArrival page
    public function onClickUpdate($id)
    {
        $dataFromForm = post(); 

        $arrival = \App\Arrival\Models\Arrival::find($id);
        $arrival->name = $dataFromForm['name'];
        $arrival->date = $dataFromForm['date'];
        $arrival->time = $dataFromForm['time'];
        $arrival->message = $dataFromForm['message'];
        $arrival->save();

        \Flash::success("Updated " . $dataFromForm['name']);
    }

    // Called by the "Delete" button when viewing the updateArrival page
    public function onClickDelete($id)
    {
        $model = \App\Arrival\Models\Arrival::find($id);
        $name = $model->name;
        $model->delete();

        \Flash::success("Deleted " . $name);

        return \Backend::redirect('app/arrival/arrivals/index');
    }
}