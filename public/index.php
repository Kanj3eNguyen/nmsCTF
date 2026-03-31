<?php

session_start();

require_once '../app/core/Router.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Model.php';
require_once '../app/core/Database.php';

require_once '../app/controllers/AuthController.php';
require_once '../app/models/User.php';

require_once '../routes/web.php';

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);