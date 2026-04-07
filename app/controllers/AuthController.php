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
        $user = $userModel->findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['error'] = 'Thong tin dang nhap khong dung.';
            $this->redirect($this->url('/login'));
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['success'] = 'Dang nhap thanh cong.';

        $this->redirect($this->url('/dashboard'));
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

        if ($email === '') {
            $_SESSION['error'] = 'Vui long nhap email.';
            $this->redirect($this->url('/forgot-password'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email khong hop le.';
            $this->redirect($this->url('/forgot-password'));
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if($user){
        $token = bin2hex(random_bytes(16));
        $expiresAt = strtotime('+15 minutes');
        $ctoken= $userModel->createToken($email, $token, $expiresAt);
        $resetLink= $this->url('/reset-password?token=' . $token);
        $sendMail= MailService::sendOtpEmail($email, $resetLink);
        }
        $_SESSION['success'] = 'Neu email ton tai, mot link reset mat khau se duoc gui den email cua ban.';
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