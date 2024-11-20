<?php 

namespace App\core;

interface IController {
    public function index();
}

class Controller implements IController
{
    protected $view;
    public function __construct()
    {
        $this->view = new View();
    }
    public function index()
    {
        
    }
}