<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);

        $user = $stmt->fetch();
        return $user ?: null;
    }
//rate limit
    public function incrementLoginAttempts(int $userId): void
    {
        $stmt = $this->db->prepare('UPDATE users SET login_attempts = COALESCE(login_attempts, 0) + 1 WHERE id = :id');
        $stmt->execute(['id' => $userId]);
    }

    public function resetLoginAttempts(int $userId): void
    {
        $stmt = $this->db->prepare('UPDATE users SET login_attempts = 0, lockout_until = NULL WHERE id = :id');
        $stmt->execute(['id' => $userId]);
    }

    public function setLockout(int $userId, int $minutes ): void
    {
        $stmt = $this->db->prepare('UPDATE users SET lockout_until = DATE_ADD(NOW(), INTERVAL :minutes MINUTE) WHERE id = :id');
        $stmt->execute(['id' => $userId, 'minutes' => $minutes]);
    }

    public function getIpData(string $ip): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM locked_ips WHERE ip_address = :ip LIMIT 1');
        $stmt->execute(['ip' => $ip]);
        
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function incrementIpAttempts(string $ip): void
    {
        $stmt = $this->db->prepare('
            INSERT INTO locked_ips (ip_address, attempts) 
            VALUES (:ip, 1)
            ON DUPLICATE KEY UPDATE attempts = attempts + 1
        ');
        $stmt->execute(['ip' => $ip]);
    }

    public function resetIpAttempts(string $ip): void
    {
        $stmt = $this->db->prepare('DELETE FROM locked_ips WHERE ip_address = :ip');
        $stmt->execute(['ip' => $ip]);
    }

    public function setIpLockout(string $ip, int $minutes = 15): void
    {
        $stmt = $this->db->prepare('
            INSERT INTO locked_ips (ip_address, attempts, lockout_until) 
            VALUES (:ip, 5, DATE_ADD(NOW(), INTERVAL :minutes MINUTE))
            ON DUPLICATE KEY UPDATE lockout_until = DATE_ADD(NOW(), INTERVAL :minutes MINUTE)
        ');
        $stmt->execute(['ip' => $ip, 'minutes' => $minutes]);
    }

    public function createAccount(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (username, email, password, created_at) VALUES (:username, :email, :password, NOW())'
        );

        return $stmt->execute([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);
    }
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch();
        return $user ?: null;
    }
    public function createOtp(string $email, string $otp, int $expires_at): bool
    {
        $this->deleteOldTokens($email);
        $stmt = $this->db->prepare(
            'INSERT INTO password_resets (email, otp, expires_at) VALUES (:email, :otp, FROM_UNIXTIME(:expires_at))'
        );

        return $stmt->execute([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => $expires_at,
        ]);
    }
    
    
    public function deleteOldTokens(string $email): bool
    {
        $stmt = $this->db->prepare('DELETE FROM password_resets WHERE email = :email');
        return $stmt->execute(['email' => $email]);
    }

    public function verifyOtp(string $email, string $otp): bool
    {
        $stmt = $this->db->prepare('SELECT * FROM password_resets WHERE email = :email AND otp = :otp AND expires_at > NOW() LIMIT 1');
        $stmt->execute([
            'email' => $email,
            'otp' => $otp
        ]);
        return $stmt->fetch() !== false;
        $this->deleteOldTokens($email);

    }

    public function createToken(string $email, string $token, int $expires_at): bool
    {
        $this->deleteOldTokens($email);
        $stmt = $this->db->prepare(
            'INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, FROM_UNIXTIME(:expires_at))'
        );

        return $stmt->execute([
            'email' => $email,
            'token' => $token,
            'expires_at' => $expires_at,
        ]);
    }

    public function updatePassword(string $email, string $hashedPassword): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET password = :password WHERE email = :email');
        return $stmt->execute([
            'password' => $hashedPassword,
            'email' => $email,
        ]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function updateProfile(int $id, string $email, string $full_name, string $phone, int $is_2fa_enabled = 0): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET email = :email, full_name = :full_name, phone = :phone, is_2fa_enabled = :is_2fa_enabled WHERE id = :id');
        return $stmt->execute([
            'email' => $email,
            'full_name' => $full_name,
            'phone' => $phone,
            'is_2fa_enabled' => $is_2fa_enabled,
            'id' => $id,
        ]);
    }
}
