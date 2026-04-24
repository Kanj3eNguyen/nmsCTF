<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Services\MailService;
class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('auth/login', [
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null,
        ]);

        unset($_SESSION['error'], $_SESSION['success']);
    }

    public function showSignup(): void
    {
        $this->view('auth/signup', [
            'error' => $_SESSION['error'] ?? null,
        ]);

        unset($_SESSION['error']);
    }

    public function login(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $_SESSION['error'] = 'Vui long nhap day du username va mat khau.';
            $this->redirect($this->url('/login'));
        }

        $userModel = new User();
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';

        
        $ipData = $userModel->getIpData($ipAddress);
        if ($ipData && !empty($ipData['lockout_until'])) {
            if (strtotime($ipData['lockout_until']) > time()) {
                $remainingWait = ceil((strtotime($ipData['lockout_until']) - time()) / 60);
                $_SESSION['error'] = "ban da bi khoa. Vui long thu lai sau {$remainingWait} phut.";
                $this->redirect($this->url('/login'));
            } else {
                
                $userModel->resetIpAttempts($ipAddress);
                $ipData = null; 
            }
        }

        $user = $userModel->findByUsername($username);

        if ($user) {
            if (!password_verify($password, $user['password'])) {
                $userModel->incrementIpAttempts($ipAddress);
                $attempts = ($ipData['attempts'] ?? 0) + 1;

                if ($attempts >= 5) {
                    $userModel->setIpLockout($ipAddress, 2); // Khóa IP 2 phút
                    $_SESSION['error'] = 'Ban da nhap sai qua nhieu lan. Ban da bi khoa trong 2 phut.';
                } else {
                    $remaining = 5 - $attempts;
                    $_SESSION['error'] = "Thong tin dang nhap khong dung.";
                }
                $this->redirect($this->url('/login'));
            } else {
                // Đăng nhập thành công, reset số lần thử trên IP
                $userModel->resetIpAttempts($ipAddress);
            }
        } else {
            $_SESSION['error'] = "Thong tin dang nhap khong dung.";
            $userModel->incrementIpAttempts($ipAddress);
            $attempts = ($ipData['attempts'] ?? 0) + 1;

            if ($attempts >= 5) {
                $userModel->setIpLockout($ipAddress, 2);
                $_SESSION['error'] = 'Ban nhap sai qua nhieu, xin vui long thu lai sau';
            } else {
                $remaining = 5 - $attempts;
                
            }
            $this->redirect($this->url('/login'));
        }

        if (isset($user['is_2fa_enabled']) && $user['is_2fa_enabled'] == 1) {
            
            $otp = sprintf("%06d", mt_rand(1, 999999));
            $expiresAt = strtotime('+15 minutes');
            $userModel->createOtp($user['email'], $otp, $expiresAt);
            MailService::sendOtpEmail($user['email'], $otp, $user['username']);

            $_SESSION['pending_2fa_user'] = $user['id'];
            $_SESSION['pending_2fa_email'] = $user['email'];
            $_SESSION['pending_2fa_username'] = $user['username'];
            $_SESSION['success'] = 'Mot ma OTP da duoc gui den email cua ban cho xac thuc 2-buoc.';
            
            $this->redirect($this->url('/login/2fa'));
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['success'] = 'Dang nhap thanh cong.';

        $this->redirect($this->url('/dashboard'));
    }

    public function show2fa(): void
    {
        if (isset($_SESSION['pending_2fa_user'])) {
        $this->view('auth/2fa', [
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null,
        ]);

        unset($_SESSION['error'], $_SESSION['success']);
        } else {
            $this->redirect($this->url('/login'));
        }
    }

    public function verify2fa(): void
    {
        if (isset($_SESSION['pending_2fa_user'])) {
        $userModel = new User();
        $otpInput = trim($_POST['otp'] ?? '');
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
        $ipData = $userModel->getIpData($ipAddress);
        if ($otpInput === '') {
            $_SESSION['error'] = 'Vui long nhap ma OTP.';
            $this->redirect($this->url('/login/2fa'));
        }
        if ($ipData && !empty($ipData['lockout_until'])) {
            if (strtotime($ipData['lockout_until']) > time()) {
                $remainingWait = ceil((strtotime($ipData['lockout_until']) - time()) / 60);
                $_SESSION['error'] = "ban da bi khoa. Vui long thu lai sau {$remainingWait} phut.";
                $this->redirect($this->url('/login'));
            } else {
                
                $userModel->resetIpAttempts($ipAddress);
                $ipData = null; 
            }
        }
        
        
        $email = $_SESSION['pending_2fa_email'];

        if ($userModel->verifyOtp($email, $otpInput)) {
            $userModel->resetIpAttempts($ipAddress);
            $userModel->deleteOldTokens($email);
            session_regenerate_id(true);
            $_SESSION['user_id'] = $_SESSION['pending_2fa_user'];
            $_SESSION['user_name'] = $_SESSION['pending_2fa_username'];
            
            unset(
                $_SESSION['pending_2fa_user'],
                $_SESSION['pending_2fa_email'],
                $_SESSION['pending_2fa_username']
            );

            $_SESSION['success'] = 'Dang nhap thanh cong.';
            $this->redirect($this->url('/dashboard'));
        } else {
            $userModel->incrementIpAttempts($ipAddress);
            $attempts = ($ipData['attempts'] ?? 0) + 1;
            if ($attempts >= 5) {
                    $userModel->setIpLockout($ipAddress, 2); // Khóa IP 2 phút
                    $_SESSION['error'] = 'Ban da nhap sai qua nhieu lan. Ban da bi khoa trong 2 phut.';
                } else {
                    $remaining = 5 - $attempts;
                    $_SESSION['error'] = "Ma OTP khong dung hoac da het han.";
                }
            $this->redirect($this->url('/login/2fa'));
        }
        } else {
            $this->redirect($this->url('/login'));
        }
    }

    public function resend2faOtp(): void
    {
        if (!isset($_SESSION['pending_2fa_user'], $_SESSION['pending_2fa_email'], $_SESSION['pending_2fa_username'])) {
            $this->redirect($this->url('/login'));
        }

        $userModel = new User();
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
        $ipData = $userModel->getIpData($ipAddress);

        if ($ipData && !empty($ipData['lockout_until'])) {
            if (strtotime($ipData['lockout_until']) > time()) {
                $remainingWait = ceil((strtotime($ipData['lockout_until']) - time()) / 60);
                $_SESSION['error'] = "Ban da bi khoa thao tac resend OTP. Vui long thu lai sau {$remainingWait} phut.";
                $this->redirect($this->url('/login/2fa'));
            } else {
                $userModel->resetIpAttempts($ipAddress);
                $ipData = null;
            }
        }

        $userModel->incrementIpAttempts($ipAddress);
        $attempts = ($ipData['attempts'] ?? 0) + 1;

        if ($attempts >= 5) {
            $userModel->setIpLockout($ipAddress, 2);
            $_SESSION['error'] = 'Ban da thao tac qua nhieu lan. Ban da bi khoa trong 2 phut.';
            $this->redirect($this->url('/login/2fa'));
        }

        $email = $_SESSION['pending_2fa_email'];
        $username = $_SESSION['pending_2fa_username'];

        $otp = sprintf('%06d', mt_rand(1, 999999));
        $expiresAt = strtotime('+5 minutes');

        $userModel->createOtp($email, $otp, $expiresAt);

        if (MailService::sendOtpEmail($email, $otp, $username)) {
            $_SESSION['success'] = 'Da gui lai ma OTP. Vui long kiem tra email cua ban.';
        } else {
            $_SESSION['error'] = 'Khong the gui lai OTP luc nay. Vui long thu lai sau.';
        }

        $this->redirect($this->url('/login/2fa'));
    }

    public function signup(): void
    {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if ($username === '' || $email === '' || $password === '' || $confirm_password === '') {
            $_SESSION['error'] = 'Vui long nhap day du thong tin.';
            $this->redirect($this->url('/signup'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email khong hop le.';
            $this->redirect($this->url('/signup'));
        }

        if (!preg_match('/^(?=.*\d).{8,}$/', $password)) {
            $_SESSION['error'] = 'Mat khau phai co it nhat 8 ky tu va chua it nhat 1 chu so.';
            $this->redirect($this->url('/signup'));
        }
    
        if ($password !== $confirm_password) {
            $_SESSION['error'] = 'Mat khau va xac nhan mat khau khong khop.';
            $this->redirect($this->url('/signup'));
        }

        $userModel = new User();

        if ($userModel->findByUsername($username)) {
            $_SESSION['error'] = 'Username da ton tai.';
            $this->redirect($this->url('/signup'));
        }

        if ($userModel->findByEmail($email)) {
            $_SESSION['error'] = 'Email da duoc su dung.';
            $this->redirect($this->url('/signup'));
        }

        $created = $userModel->createAccount([
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        if (!$created) {
            $_SESSION['error'] = 'Khong the tao tai khoan, vui long thu lai.';
            $this->redirect($this->url('/signup'));
        }

        $_SESSION['success'] = 'Dang ky thanh cong. Vui long dang nhap.';
        $this->redirect($this->url('/login'));
    }
//forgot
    public function showForgotPassword(): void
    {
        $this->view('auth/forgot_password', [
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null,
        ]);

        unset($_SESSION['error'], $_SESSION['success']);
    }
    public function forgotPassword(): void
    {
        $email = trim($_POST['email'] ?? '');
        $action = $_POST['action'] ?? 'send_otp';
        $otpInput = trim($_POST['otp'] ?? '');

        if ($email === '') {
            $_SESSION['error'] = 'Vui long nhap email.';
            $this->redirect($this->url('/forgot-password'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email khong hop le.';
            $this->redirect($this->url('/forgot-password'));
        }

        $userModel = new User();
        $check = $userModel->findByEmail($email);

        if ($action === 'verify_otp') {
            if ($otpInput === '') {
                $_SESSION['error'] = 'Vui long nhap ma OTP.';
                $this->redirect($this->url('/forgot-password'));
            }

            if ($userModel->verifyOtp($email, $otpInput)) {
                $userModel->deleteOldTokens($email);
                $_SESSION['reset_email'] = $email;
                $_SESSION['success'] = 'Xac thuc thanh cong. Vui long dat lai mat khau.';
                $this->redirect($this->url('/reset-password')); 
            } else {
                $_SESSION['error'] = 'Ma OTP khong dung hoac da het han.';
                $this->redirect($this->url('/forgot-password'));
            }
        }

        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
        $ipData = $userModel->getIpData($ipAddress);

        if ($ipData && !empty($ipData['lockout_until'])) {
            if (strtotime($ipData['lockout_until']) > time()) {
                $remainingWait = ceil((strtotime($ipData['lockout_until']) - time()) / 60);
                $_SESSION['error'] = "Ban da thao tac qua nhieu lan. Vui long thu lai sau {$remainingWait} phut.";
                $this->redirect($this->url('/forgot-password'));
            } else {
                $userModel->resetIpAttempts($ipAddress);
                $ipData = null;
            }
        }

        $userModel->incrementIpAttempts($ipAddress);
        $attempts = ($ipData['attempts'] ?? 0) + 1;

        if ($attempts >= 5) {
            $userModel->setIpLockout($ipAddress, 2);
            $_SESSION['error'] = 'Ban da thao tac qua nhieu lan. Ban da bi khoa trong 2 phut.';
            $this->redirect($this->url('/forgot-password'));
        }

        // Logic send_otp
        if($check){
            $forgotname = $check['username'];
            
            // Create an OTP separately
            $otp = sprintf("%06d", mt_rand(1, 999999));
            $expiresAt = strtotime('+15 minutes');
            $userModel->createOtp($email, $otp, $expiresAt);
            
            $sendMail= MailService::sendOtpEmail($email, $otp, $forgotname);
        }
        
        $_SESSION['success'] = 'Neu email ton tai, mot ma OTP se duoc gui den email cua ban.';
        $this->redirect($this->url('/forgot-password'));
    }
    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        $this->redirect($this->url('/login'));
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