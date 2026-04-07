<?php

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;

$router = new Router();
//login
$router->get('/', [AuthController::class, 'showLogin']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
//forgot
$router->get('/forgot-password', [AuthController::class, 'showForgotPassword']);
$router->post('/forgot-password', [AuthController::class, 'forgotPassword']);

//reset
$router->get('/reset-password', [AuthController::class, 'showResetPassword']);
$router->post('/reset-password', [AuthController::class, 'resetPassword']);


//signup
$router->get('/signup', [AuthController::class, 'showSignup']);
$router->post('/signup', [AuthController::class, 'signup']);
//logout
$router->get('/logout', [AuthController::class, 'logout']);
//dash
$router->get('/dashboard', [DashboardController::class, 'showDashboard']);