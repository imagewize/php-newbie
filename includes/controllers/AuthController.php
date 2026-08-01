<?php

class AuthController {
    private User $user;

    public function __construct() {
        $this->user = new User();
    }

    public function login(array $postData): array {
        if (isset($postData['login'])) {
            $loginUser = $this->user->login($postData['email'], $postData['password']);
            if ($loginUser) {
                return ['redirect' => 'index.php'];
            }
            return [
                'error' => 'Invalid email or password',
                'action' => 'login'
            ];
        }
        
        return ['action' => 'login'];
    }

    public function logout(): array {
        $this->user->logout();
        return ['redirect' => 'index.php'];
    }
}
