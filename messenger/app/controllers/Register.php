<?php 

namespace App\controllers;

use App\core\Controller;

class register extends Controller
{

    public function index()
    {
        $this->view->render('register.php', 'template.php');
    }
    
}