<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();



require_once '../app/core/Router.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Model.php';
require_once '../app/core/Database.php';

require_once '../app/controllers/AuthController.php';
require_once '../app/models/User.php';

require_once '../routes/web.php';

require_once '../app/controllers/DashboardController.php';


$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);