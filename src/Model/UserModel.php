<?php

namespace App\Model;

readonly class UserModel extends Model
{
    public function login(
        string $username,
        string $password,
    ): array|false {
        $user = $this->fetch('SELECT * FROM users WHERE username = :username LIMIT 1', [
            new Parameter(':username', $username),
        ]);

        if (empty($user) || !password_verify($password, $user['password'])) {
            return false;
        }

        unset($user['password']);

        return $user;
    }
}
