<?php 

namespace App\core;

define('CONTROLLERS_NAMESPACE', 'App\\controllers\\');
class Route
{
    
    public static function start()
    {
        $controllerClassname = 'chat';
        $actionName = 'index';
        $payload = [];

        $routes = explode('/', $_SERVER["REQUEST_URI"]);
        
        if(!empty($routes[1])) {
            $controllerClassname = $routes[1];
        }

        if(!empty($routes[2])) {
            $actionName = $routes[2];
        }

        if(!empty($routes[3])) {
            $payload = array_slice($routes, 3);
        }

        $controllerName = CONTROLLERS_NAMESPACE . ucfirst($controllerClassname);

        $controllerFile = ucfirst(strtolower($controllerClassname)) . '.php';

        $controllerPath = CONTROLLER . $controllerFile;
        if(file_exists($controllerPath)) {
            include_once $controllerPath;
        } else {
            Route::error();
        }

        $controller = new $controllerName();
        if(method_exists($controller, $actionName)) {
            $controller->$actionName($payload);
        } else {
            Route::error();
        }
    }

    public static function error()
    {
        header('HTTP 404 Not Found');
        header('Status 404 Not Found');
        header('Location:/error');
    }

}
