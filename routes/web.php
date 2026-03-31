<?php

use App\Core\Router;
use App\Controllers\AuthController;

$router = new Router();

$router->get('/', [AuthController::class, 'showLogin']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);

$router->get('/signup', [AuthController::class, 'showSignup']);
$router->post('/signup', [AuthController::class, 'signup']);

$router->get('/logout', [AuthController::class, 'logout']);