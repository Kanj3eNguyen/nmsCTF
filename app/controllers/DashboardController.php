<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function showDashboard(): void
    {
        // Cần gọi session_start() trước nếu chưa được tự động kích hoạt
        if (!isset($_SESSION['user_id'])) {
            // Hoặc sử dụng hàm redirect theo cách bạn đã định nghĩa trong controller của mình
            $_SESSION['error'] = 'Bạn cần đăng nhập để truy cập trang này.';
            $this->redirect($this->url('/login')); // Đổi thành URL phù hợp nếu project của bạn chạy trên một thư mục con
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
