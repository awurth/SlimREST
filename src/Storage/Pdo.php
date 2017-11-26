<?php

namespace App\Storage;

use OAuth2\Storage\Pdo as BasePdo;

class Pdo extends BasePdo
{
    public function getUser($username)
    {
        $stmt = $this->db->prepare($sql = sprintf('SELECT * from %s where username=:username', $this->config['user_table']));
        $stmt->execute(['username' => $username]);

        if (!$userInfo = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            return false;
        }

        return array_merge([
            'user_id' => $userInfo['id']
        ], $userInfo);
    }

    protected function checkPassword($user, $password)
    {
        return password_verify($password, $user['password']);
    }

    protected function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
