<?php namespace App\Arrival\Controllers;

use App\Arrival\Models\Arrival;
use Backend;
use BackendMenu;
use Backend\Classes\Controller;
use Flash;
use Seld\PharUtils\Timestamps;
use October\Rain\Exception\ValidationException ;

class Arrivals extends Controller
{

    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('App.Arrival', 'main-menu-item', );
    }

    public function index()
    {
        // I'm leaving the following commented code here, because even though it'd work
        // (provided you replace the current $this->listRender() with $listWidget->render() )
        // I couldnt get the toolbar to work using this method: 

        // $config = $this->makeConfig('$/app/arrival/models/arrival/columns.yaml');
        // $config->model = new \App\Arrival\Models\Arrival;

        // $widget = $this->makeWidget('Backend\Widgets\Lists', $config);
        // $widget->bindToController();
        // $widget->recordUrl = 'app/arrival/arrivals/updatearrival/:id';
        // $widget->showCheckboxes = true;
        // $this->vars['listWidget'] = $widget;
        
        $this->asExtension('ListController')->index();
    }

    public function insert()
    {
        $config = $this->makeConfig('$/app/arrival/models/arrival/fields.yaml');
        $config->model = new \App\Arrival\Models\Arrival;
        $widget = $this->makeWidget('Backend\Widgets\Form', $config);
        $this->vars['formWidget'] = $widget;
    }

    public function update($id)
    {
        $config = $this->makeConfig('$/app/arrival/models/arrival/fields.yaml');
        $config->model = \App\Arrival\Models\Arrival::find($id);
        $widget = $this->makeWidget('Backend\Widgets\Form', $config);
        $this->vars['formWidget'] = $widget;
    }

    public function onClickInsert()
    {    
        $dataFromForm = post(); // all the form data is returned by post, basicaly the same as the superglobal $_POST

        $date = 0;
        $time = 0;

        $timestamp = strtotime($dataFromForm['datetime']);

        if (!$timestamp)
        {
            \Flash::error("Invalid date");
            return;   
        }
        // The weird thing is, if you input an invalid date, the form sends the string "Invalid date".
        // However, if you type an invalid time, it just converts it to 0:00
        $date = date('d/m/Y', $timestamp);
        $time = date('h/i/s', $timestamp);
        
        // if (!$timestamp)
        // {
        //     \Flash::error("Invalid date or time. Received date and time: " . $dataFromForm["datetime"]);
        //     return;
        // }
        // $date = date('d/m/Y', $timestamp);  
        // $time = date('h:i:s', $timestamp);

        $arrival = new Arrival;
        $arrival->name = $dataFromForm['name'];
        $arrival->date = $date;
        $arrival->time = $time;
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

    public function onMultiDelete() {
        
    }

    // Called upon clicking on the "Insert new student" button on the index page
    public function onRedirectToInsert() 
    {
        return \Backend::redirect('app/arrival/arrivals/insertarrival');
    }

    private function IsDateValid($date)
    {
        // The received date will be in the format "yyyy-mm-dd hh:mm:ss", despite having set
        // the mode in the field.yaml file to "date" and not "datetime"
        // (This is a bug in ocms)

        $timestamp = strtotime($date);
        if (!$timestamp)
        {
            return false;
        }

        $onlyDate = date('d/m/Y', $timestamp);
        \Flash::success("@" . $onlyDate . "@");
        $pattern = "#13/11/2000#";
        return preg_match($pattern, $date);
    }

    private function IsTimeValid($time) 
    {
        return true;
    }
    #region old
    // public function __construct()
    // {
    //     parent::__construct();
    //     BackendMenu::setContext('App.Arrival', 'main-menu-item', );
    // }

    // public $implement = [
    //     \Backend\Behaviors\FormController::class,
    //     \Backend\Behaviors\ListController::class
    // ];

    // public $listConfig = 'config_list.yaml';
    // public $formConfig = 'config_form.yaml';

    // // Shows the list of all arrivals
    // public function index()
    // {
    //     // $config = $this->makeConfig('$/app/arrival/models/arrival/columns.yaml');
    //     // $config->model = new \App\Arrival\Models\Arrival;

    //     //$widget = $this->makeWidget('Backend\Widgets\Lists', $config);
    //     //$widget->bindToController();
    //     //$widget->recordUrl = 'app/arrival/arrivals/updatearrival/:id';
    //     //$widget->showCheckboxes = true;
    //     //$this->vars['listWidget'] = $widget;

    //     $this->asExtension('ListController')->index();
    // }

    // // Called upon clicking on the "Insert new student" button on the index page
    // public function onRedirectToInsert() 
    // {
    //     return \Backend::redirect('app/arrival/arrivals/insertarrival');
    // }

    // // Shows a form for inserting a new arrival
    // public function insertArrival()
    // {
    //     $config = $this->makeConfig('$/app/arrival/models/arrival/fields.yaml');
    //     $config->model = new \App\Arrival\Models\Arrival;
    //     $widget = $this->makeWidget('Backend\Widgets\Form', $config);
    //     $this->vars['formWidget'] = $widget;
    // }

    // // Shows a form for updating an existing arrival
    // public function updateArrival($id) 
    // {    
    //     $config = $this->makeConfig('$/app/arrival/models/arrival/fields.yaml');
    //     $config->model = \App\Arrival\Models\Arrival::find($id);
    //     $widget = $this->makeWidget('Backend\Widgets\Form', $config);
    //     $this->vars['formWidget'] = $widget;
    // }

    // // Called by the "Insert" button when viewing the insertArrival page
    // public function onClickInsert()
    // {    
    //     $dataFromForm = post(); // all the form data is returned by post, basicaly the same as the superglobal $_POST

    //     $date = 0;
    //     $time = 0;

    //     $timestamp = strtotime($dataFromForm['datetime']);

    //     if (!$timestamp)
    //     {
    //         \Flash::error("Invalid date");
    //         return;   
    //     }
    //     // The weird thing is, if you input an invalid date, the form sends the string "Invalid date".
    //     // However, if you type an invalid time, it just converts it to 0:00
    //     $date = date('d/m/Y', $timestamp);
    //     $time = date('h/i/s', $timestamp);
        
    //     // if (!$timestamp)
    //     // {
    //     //     \Flash::error("Invalid date or time. Received date and time: " . $dataFromForm["datetime"]);
    //     //     return;
    //     // }
    //     // $date = date('d/m/Y', $timestamp);  
    //     // $time = date('h:i:s', $timestamp);

    //     $arrival = new Arrival;
    //     $arrival->name = $dataFromForm['name'];
    //     $arrival->date = $date;
    //     $arrival->time = $time;
    //     $arrival->message = $dataFromForm['message'];
    //     $arrival->save();

    //     \Flash::success("Added " . $dataFromForm['name']);
    // }

    // // Called by the "Update" button when viewing the updateArrival page
    // public function onClickUpdate($id)
    // {
    //     $dataFromForm = post(); 

    //     $arrival = \App\Arrival\Models\Arrival::find($id);
    //     $arrival->name = $dataFromForm['name'];
    //     $arrival->date = $dataFromForm['date'];
    //     $arrival->time = $dataFromForm['time'];
    //     $arrival->message = $dataFromForm['message'];
    //     $arrival->save();

    //     \Flash::success("Updated " . $dataFromForm['name']);
    // }

    // // Called by the "Delete" button when viewing the updateArrival page
    // public function onClickDelete($id)
    // {
    //     $model = \App\Arrival\Models\Arrival::find($id);
    //     $name = $model->name;
    //     $model->delete();

    //     \Flash::success("Deleted " . $name);

    //     return \Backend::redirect('app/arrival/arrivals/index');
    // }

    // public function onMultiDelete() {
        
    // }

    // private function IsDateValid($date)
    // {
    //     // The received date will be in the format "yyyy-mm-dd hh:mm:ss", despite having set
    //     // the mode in the field.yaml file to "date" and not "datetime"
    //     // (This is a bug in ocms)

    //     $timestamp = strtotime($date);
    //     if (!$timestamp)
    //     {
    //         return false;
    //     }

    //     $onlyDate = date('d/m/Y', $timestamp);
    //     \Flash::success("@" . $onlyDate . "@");
    //     $pattern = "#13/11/2000#";
    //     return preg_match($pattern, $date);
    // }

    // private function IsTimeValid($time) 
    // {
    //     return true;
    // }
    #endregion old
}