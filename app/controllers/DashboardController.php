<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function showDashboard(): void
    {
        
        if (!isset($_SESSION['user_id'])) {
            
            $_SESSION['error'] = 'Bạn cần đăng nhập để truy cập trang này.';
            $this->redirect($this->url('/login')); 
            exit();
        }

        $this->view('home/dashboard', [
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null,
        ]);

        unset($_SESSION['error'], $_SESSION['success']);
    }
    private function url(string $path): string
    {
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $base = rtrim(dirname($scriptName), '/');

        if ($base === '' || $base === '.') {
            return $path;
        }

        return $base . $path;
    }

}
