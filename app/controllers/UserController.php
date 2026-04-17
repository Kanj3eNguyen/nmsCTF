<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Services\MailService;
class UserController extends Controller
{
    //resetpass
    public function showResetPassword(): void
    {
        
        if (!isset($_SESSION['reset_email'])) {
            $_SESSION['error'] = 'Ban phai xac thuc OTP truoc khi doi mat khau.';
            $this->redirect($this->url('/forgot-password'));
        }

        $this->view('auth/reset_password', [
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null,
        ]);

        unset($_SESSION['error'], $_SESSION['success']);
    }


    public function resetPassword(): void
    {
        if (!isset($_SESSION['reset_email'])) {
            $this->redirect($this->url('/forgot-password'));
        }

        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $email = $_SESSION['reset_email'];

        if ($password === '' || $confirmPassword === '') {
            $_SESSION['error'] = 'Vui long nhap mat khau moi.';
            $this->redirect($this->url('/reset-password'));
        }

        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'Mat khau xac nhan khong khop.';
            $this->redirect($this->url('/reset-password'));
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);
        
        if ($user) {            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $userModel->updatePassword($email, $hashedPassword);
            unset($_SESSION['reset_email']);
            $userModel->deleteOldTokens($email);
            
            $_SESSION['success'] = 'Doi mat khau thanh cong. Vui long dang nhap lai!';
            $this->redirect($this->url('/login'));
        } else {
            $_SESSION['error'] = 'Tai khoan khong ton tai.';
            $this->redirect($this->url('/reset-password'));
        }
    }

    // profile
    public function showProfile(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect($this->url('/login'));
        }

        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);

        $this->view('user/profile', [
            'user' => $user,
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null,
        ]);

        unset($_SESSION['error'], $_SESSION['success']);
    }
    public function changePassword(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect($this->url('/login'));
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $_SESSION['error'] = 'Vui long nhap day du thong tin.';
            $this->redirect($this->url('/profile'));
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'Mat khau moi va xac nhan mat khau khong khop.';
            $this->redirect($this->url('/profile'));
        }

        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            $_SESSION['error'] = 'Mat khau hien tai khong chinh xac.';
            $this->redirect($this->url('/profile'));
        }
            $otp = sprintf("%06d", mt_rand(1, 999999));
            $expiresAt = strtotime('+15 minutes');
            $userModel->createOtp($user['email'], $otp, $expiresAt);
            MailService::sendOtpEmail($user['email'], $otp, $user['username']);
            $_SESSION['pending_password_change'] = true;
            $_SESSION['success'] = 'Mot ma OTP da duoc gui den email cua ban cho xac thuc thay doi mat khau.';
            
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        if ($userModel->updatePassword($user['email'], $hashedNewPassword)) {
            $_SESSION['success'] = 'Doi mat khau thanh cong.';
        } else {
            $_SESSION['error'] = 'Co loi xay ra, vui long thu lai.';
        }

        $this->redirect($this->url('/profile'));
    }
  
    public function updateProfile(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect($this->url('/login'));
        }

        $email = trim($_POST['email'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $is2faEnabled = isset($_POST['is_2fa_enabled']) ? 1 : 0;
        $userId = $_SESSION['user_id'];

        if ($email === '') {
            $_SESSION['error'] = 'Vui long nhap email.';
            $this->redirect($this->url('/profile'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email khong hop le.';
            $this->redirect($this->url('/profile'));
        }

        $userModel = new User();
        
        
        $existing = $userModel->findByEmail($email);
        if ($existing && $existing['id'] !== $userId) {
            $_SESSION['error'] = 'Email nay da duoc su dung boi tai khoan khac.';
            $this->redirect($this->url('/profile'));
        }

        if ($userModel->updateProfile($userId, $email, $fullName, $phone, $is2faEnabled)) {
            $_SESSION['success'] = 'Cap nhat thong tin thanh cong.';
        } else {
            $_SESSION['error'] = 'Co loi xay ra, vui long thu lai.';
        }

        $this->redirect($this->url('/profile'));
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
