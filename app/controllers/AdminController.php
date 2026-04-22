<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Admin;
use App\Models\User;

class AdminController extends Controller
{
    public function showAdminDashboard(): void
    {
        $this->requireAdmin();

        $adminModel = new Admin();
        $categories = $adminModel->getCategories();

        $this->view('admin/dashboard', [
            'categories' => $categories,
            'oldInput' => $_SESSION['old_input'] ?? [],
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null,
        ]);

        unset($_SESSION['error'], $_SESSION['success'], $_SESSION['old_input']);
    }

    public function createChallenge(): void
    {
        $this->requireAdmin();

        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $newCategoryName = trim((string) ($_POST['new_category_name'] ?? ''));
        $title = trim((string) ($_POST['title'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $points = (int) ($_POST['points'] ?? 100);
        $difficulty = trim((string) ($_POST['difficulty'] ?? 'easy'));
        $flag = trim((string) ($_POST['flag'] ?? ''));
        $hint = trim((string) ($_POST['hint'] ?? ''));
        $filePath = trim((string) ($_POST['file_path'] ?? ''));
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $_SESSION['old_input'] = [
            'category_id' => $categoryId,
            'new_category_name' => $newCategoryName,
            'title' => $title,
            'description' => $description,
            'points' => $points,
            'difficulty' => $difficulty,
            'hint' => $hint,
            'file_path' => $filePath,
            'is_active' => $isActive,
        ];

        if ($title === '' || $description === '' || $flag === '') {
            $_SESSION['error'] = 'Title, description, va flag là bắt buộc.';
            $this->redirect($this->url('/admin'));
            return;
        }

        if ($points <= 0) {
            $_SESSION['error'] = 'Points phải lớn hơn 0.';
            $this->redirect($this->url('/admin'));
            return;
        }

        $validDifficulties = ['easy', 'medium', 'hard'];
        if (!in_array($difficulty, $validDifficulties, true)) {
            $_SESSION['error'] = 'Difficulty không hợp lệ.';
            $this->redirect($this->url('/admin'));
            return;
        }

        $adminModel = new Admin();

        if ($newCategoryName !== '') {
            $existingCategory = $adminModel->findCategoryByName($newCategoryName);
            if ($existingCategory) {
                $categoryId = (int) $existingCategory['id'];
            } else {
                $categoryId = $adminModel->createCategory($newCategoryName);
            }
        }

        if ($categoryId <= 0 || $adminModel->findCategoryById($categoryId) === null) {
            $_SESSION['error'] = 'Vui lòng chọn category hợp lệ hoặc tạo category mới.';
            $this->redirect($this->url('/admin'));
            return;
        }

        $created = $adminModel->createChallenge([
            'category_id' => $categoryId,
            'title' => $title,
            'description' => $description,
            'points' => $points,
            'difficulty' => $difficulty,
            'flag_hash' => password_hash($flag, PASSWORD_DEFAULT),
            'hint' => $hint,
            'file_path' => $filePath,
            'is_active' => $isActive,
        ]);

        if (!$created) {
            $_SESSION['error'] = 'Không thể tạo challenge. Vui lòng thử lại.';
            $this->redirect($this->url('/admin'));
            return;
        }

        unset($_SESSION['old_input']);
        $_SESSION['success'] = 'Đã tạo challenge thành công.';
        $this->redirect($this->url('/admin'));
    }

    private function requireAdmin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Bạn cần đăng nhập để truy cập trang này.';
            $this->redirect($this->url('/login'));
            exit();
        }

        $userModel = new User();
        if (!$userModel->isAdmin((int) $_SESSION['user_id'])) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang admin.';
            $this->redirect($this->url('/dashboard'));
            exit();
        }
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
