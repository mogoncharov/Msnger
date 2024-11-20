<?php 

namespace App\controllers;

use App\core\Controller;

class Login extends Controller
{

    public function index()
    {
        $this->view->render('login.php', 'template.php');
    }
    
}