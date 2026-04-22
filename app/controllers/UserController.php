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

        if (isset($_SESSION['pending_profile_update']) && is_array($_SESSION['pending_profile_update'])) {
            $pending = $_SESSION['pending_profile_update'];
            $user['email'] = $pending['email'] ?? ($user['email'] ?? '');
            $user['full_name'] = $pending['full_name'] ?? ($user['full_name'] ?? '');
            $user['phone'] = $pending['phone'] ?? ($user['phone'] ?? '');
            $user['is_2fa_enabled'] = $pending['is_2fa_enabled'] ?? ($user['is_2fa_enabled'] ?? 0);
        }

        $this->view('user/profile', [
            'user' => $user,
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null,
        ]);

        unset($_SESSION['error'], $_SESSION['success']);
    }
    
  
    public function updateProfile(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect($this->url('/login'));
        }

        $action = $_POST['action'] ?? 'send_otp';
        $email = trim($_POST['email'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $is2faEnabled = isset($_POST['is_2fa_enabled']) ? 1 : 0;
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $otpInput = trim($_POST['otp'] ?? '');
        $userId = $_SESSION['user_id'];

        $userModel = new User();
        $user = $userModel->findById($userId);

        if (!$user) {
            $_SESSION['error'] = 'Tai khoan khong ton tai.';
            $this->redirect($this->url('/login'));
        }

        if ($email === '') {
            $_SESSION['error'] = 'Vui long nhap email.';
            $this->redirect($this->url('/profile'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email khong hop le.';
            $this->redirect($this->url('/profile'));
        }

        $existing = $userModel->findByEmail($email);
        if ($existing && $existing['id'] !== $userId) {
            $_SESSION['error'] = 'Email nay da duoc su dung boi tai khoan khac.';
            $this->redirect($this->url('/profile'));
        }

        $isPasswordChangeRequested = ($currentPassword !== '' || $newPassword !== '' || $confirmPassword !== '');

        if ($isPasswordChangeRequested) {
            if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
                $_SESSION['error'] = 'Vui long nhap day du thong tin doi mat khau.';
                $this->redirect($this->url('/profile'));
            }

            if (!password_verify($currentPassword, $user['password'])) {
                $_SESSION['error'] = 'Mat khau hien tai khong chinh xac.';
                $this->redirect($this->url('/profile'));
            }

            if ($newPassword !== $confirmPassword) {
                $_SESSION['error'] = 'Mat khau moi va xac nhan mat khau khong khop.';
                $this->redirect($this->url('/profile'));
            }
        }

        if ($action === 'send_otp') {
            $pendingProfileUpdate = [
                'email' => $email,
                'full_name' => $fullName,
                'phone' => $phone,
                'is_2fa_enabled' => $is2faEnabled,
            ];

            if ($isPasswordChangeRequested) {
                $pendingProfileUpdate['new_password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            $_SESSION['pending_profile_update'] = $pendingProfileUpdate;

            $otp = sprintf('%06d', mt_rand(1, 999999));
            $expiresAt = strtotime('+15 minutes');
            $userModel->createOtp($user['email'], $otp, $expiresAt);
            MailService::sendOtpEmail($user['email'], $otp, $user['username']);

            $_SESSION['success'] = 'Mot ma OTP da duoc gui den email cua ban de xac thuc cap nhat profile.';
            $this->redirect($this->url('/profile'));
        }

        if ($action !== 'verify_update') {
            $_SESSION['error'] = 'Thao tac khong hop le.';
            $this->redirect($this->url('/profile'));
        }

        if (!isset($_SESSION['pending_profile_update']) || !is_array($_SESSION['pending_profile_update'])) {
            $_SESSION['error'] = 'Vui long gui OTP truoc khi cap nhat profile.';
            $this->redirect($this->url('/profile'));
        }

        if ($otpInput === '') {
            $_SESSION['error'] = 'Vui long nhap ma OTP de xac thuc.';
            $this->redirect($this->url('/profile'));
        }

        if (!$userModel->verifyOtp($user['email'], $otpInput)) {
            $_SESSION['error'] = 'Ma OTP khong dung hoac da het han.';
            $this->redirect($this->url('/profile'));
        }

        $pendingData = $_SESSION['pending_profile_update'];
        $pendingEmail = trim((string) ($pendingData['email'] ?? ''));
        $pendingFullName = trim((string) ($pendingData['full_name'] ?? ''));
        $pendingPhone = trim((string) ($pendingData['phone'] ?? ''));
        $pending2faEnabled = (int) ($pendingData['is_2fa_enabled'] ?? 0);
        $pendingPasswordHash = $pendingData['new_password_hash'] ?? null;

        if ($pendingEmail === '') {
            unset($_SESSION['pending_profile_update']);
            $_SESSION['error'] = 'Du lieu cap nhat profile khong hop le. Vui long thu lai.';
            $this->redirect($this->url('/profile'));
        }

        $existingPending = $userModel->findByEmail($pendingEmail);
        if ($existingPending && $existingPending['id'] !== $userId) {
            unset($_SESSION['pending_profile_update']);
            $_SESSION['error'] = 'Email nay da duoc su dung boi tai khoan khac.';
            $this->redirect($this->url('/profile'));
        }

        if ($userModel->updateProfile($userId, $pendingEmail, $pendingFullName, $pendingPhone, $pending2faEnabled)) {
            if (is_string($pendingPasswordHash) && $pendingPasswordHash !== '') {
                $userModel->updatePassword($pendingEmail, $pendingPasswordHash);
            }

            $userModel->deleteOldTokens($user['email']);
            unset($_SESSION['pending_profile_update']);
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
