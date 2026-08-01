<?php
class User {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll(): array {
        return $this->db->fetchAll("SELECT * FROM users ORDER BY created_at DESC");
    }

    public function getById(int $id): array|false {
        return $this->db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
    }

    public function getByEmail(string $email): array|false {
        return $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
    }

    public function create(array $data): int|false {
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $this->db->query(
            "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)",
            [$data['name'], $data['email'], $passwordHash, $data['role'] ?? 'user']
        );
        
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $this->db->query(
                "UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE id = ?",
                [$data['name'], $data['email'], $data['password'], $data['role'], $id]
            );
        } else {
            $this->db->query(
                "UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?",
                [$data['name'], $data['email'], $data['role'], $id]
            );
        }
        return true;
    }

    public function delete(int $id): bool {
        return (bool) $this->db->query("DELETE FROM users WHERE id = ?", [$id]);
    }

    public function login(string $email, string $password): array|false {
        $user = $this->getByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];
            return $user;
        }
        return false;
    }

    public function logout(): void {
        session_unset();
        session_destroy();
    }

    public function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    public function getCurrentUser(): array|null {
        if (!$this->isLoggedIn()) {
            return null;
        }
        return $this->getById((int)$_SESSION['user_id']);
    }
}
