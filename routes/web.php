<?php

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\UserController;
use App\Models\User;

$router = new Router();
//login
$router->get('/', [AuthController::class, 'showLogin']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);

//2FA login
$router->get('/login/2fa', [AuthController::class, 'show2fa']);
$router->post('/login/2fa', [AuthController::class, 'verify2fa']);
$router->post('/login/2fa/resend', [AuthController::class, 'resend2faOtp']);

//forgot
$router->get('/forgot-password', [AuthController::class, 'showForgotPassword']);
$router->post('/forgot-password', [AuthController::class, 'forgotPassword']);

//reset
$router->get('/reset-password', [UserController::class, 'showResetPassword']);
$router->post('/reset-password', [UserController::class, 'resetPassword']);


//signup
$router->get('/signup', [AuthController::class, 'showSignup']);
$router->post('/signup', [AuthController::class, 'signup']);
//logout
$router->get('/logout', [AuthController::class, 'logout']);
//dash
$router->get('/dashboard', [DashboardController::class, 'showDashboard']);

//profile
$router->get('/profile', [UserController::class, 'showProfile']);
$router->post('/profile/update', [UserController::class, 'updateProfile']);
