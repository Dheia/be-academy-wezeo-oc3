<?php namespace App\Arrival\Controllers;

use Backend;
use BackendMenu;
use Backend\Classes\Controller;

class Arrivals extends Controller
{
    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('App.Arrival', 'main-menu-item');
    }

    public function index() {
        return 'hello';
    }
}