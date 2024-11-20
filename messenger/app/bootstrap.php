<?php 

namespace App;

use App\controllers\Config;
use App\controllers\SQLiteConnection;
use App\controllers\SQLiteCreateTable;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

session_start();
// unset($_SESSION);
require_once 'core' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
require_once dirname(__DIR__, 1) . '/vendor/autoload.php';

if(!file_exists(Config::PATH_TO_SQLITE_FILE)) {
    $file = fopen(Config::PATH_TO_SQLITE_FILE, 'a+');
    fclose($file);
    $sqlite = new SQLiteCreateTable((new SQLiteConnection())->connect());
    
    $sqlite->createTables();
}

core\Route::start(); // запускаем маршрутизатор