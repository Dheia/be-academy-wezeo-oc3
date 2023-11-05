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
        $widget->bindToController();
        $widget->recordUrl = 'app/arrival/arrivals/update_arrival/:id';
        $this->vars['listWidget'] = $widget;
    }

    // TODO: Apply a consistent style! Some methods are snake_case and some camelCase!!!

    public function onRedirectToInsert() {

        // redirects the user to the insert_arrival view

        return \Backend::redirect('app/arrival/arrivals/insert_arrival');
    }

    // without this method, the redirect in onRedirectToUpdate would not work.
    // or maybe it would? either way, we need this method so we can create a 
    // widget that we pass to the update_arrival view
    public function insert_arrival() {
        $config = $this->makeConfig('$/app/arrival/models/arrival/fields.yaml');
        $config->model = new \App\Arrival\Models\Arrival;
        $widget = $this->makeWidget('Backend\Widgets\Form', $config);
        $this->vars['formWidget'] = $widget;
    }

    public function update_arrival($id) {
        
        $config = $this->makeConfig('$/app/arrival/models/arrival/fields.yaml');
        $config->model = \App\Arrival\Models\Arrival::find($id);
        $widget = $this->makeWidget('Backend\Widgets\Form', $config);
        $this->vars['formWidget'] = $widget;
    }

    public function onClickInsert() {
        
        $dataFromForm = post(); // all the form data is returned by post, basicaly the same as the superglobal $_POST

        $arrival = new Arrival;
        $arrival->name = $dataFromForm['name'];
        $arrival->date = $dataFromForm['date'];
        $arrival->time = $dataFromForm['time'];
        $arrival->message = $dataFromForm['message'];
        $arrival->save();

        \Flash::success("Added " . $dataFromForm['name']);
    }

    public function onClickUpdate($id) {

        $dataFromForm = post(); // all the form data is returned by post, basicaly the same as the superglobal $_POST

        $arrival = \App\Arrival\Models\Arrival::find($id);
        $arrival->name = $dataFromForm['name'];
        $arrival->date = $dataFromForm['date'];
        $arrival->time = $dataFromForm['time'];
        $arrival->message = $dataFromForm['message'];
        $arrival->save();

        \Flash::success("Updated " . $dataFromForm['name']);

    }

    public function onClickDelete($id) {
        $model = \App\Arrival\Models\Arrival::find($id);
        $name = $model->name;
        $model->delete();

        \Flash::success("Deleted " . $name);

        return \Backend::redirect('app/arrival/arrivals/index');
    }
}