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

    public function updateProfile(int $id, string $email): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET email = :email WHERE id = :id');
        return $stmt->execute([
            'email' => $email,
            'id' => $id,
        ]);
    }
}
