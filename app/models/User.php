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

    public function create(array $data): bool
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
}
