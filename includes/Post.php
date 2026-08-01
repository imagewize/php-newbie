<?php
class Post {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll(): array {
        return $this->db->fetchAll(
            "SELECT p.*, u.name as author_name, u.id as author_id 
             FROM posts p 
             JOIN users u ON p.user_id = u.id 
             ORDER BY p.created_at DESC"
        );
    }

    public function getById(int $id): array|false {
        return $this->db->fetch(
            "SELECT p.*, u.name as author_name, u.id as author_id 
             FROM posts p 
             JOIN users u ON p.user_id = u.id 
             WHERE p.id = ?",
            [$id]
        );
    }

    public function getByUser(int $userId): array {
        return $this->db->fetchAll(
            "SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        );
    }

    public function create(array $data): int|false {
        $user = new User();
        $currentUser = $user->getCurrentUser();
        
        if (!$currentUser) {
            return false;
        }

        $this->db->query(
            "INSERT INTO posts (title, content, user_id, status) VALUES (?, ?, ?, ?)",
            [$data['title'], $data['content'], $currentUser['id'], $data['status'] ?? 'published']
        );
        
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $user = new User();
        $currentUser = $user->getCurrentUser();
        
        if (!$currentUser) {
            return false;
        }

        // Check if user owns the post or is admin
        $post = $this->getById($id);
        if (!$post || ($post['user_id'] != $currentUser['id'] && $currentUser['role'] != 'admin')) {
            return false;
        }

        $this->db->query(
            "UPDATE posts SET title = ?, content = ?, status = ? WHERE id = ?",
            [$data['title'], $data['content'], $data['status'] ?? $post['status'], $id]
        );
        
        return true;
    }

    public function delete(int $id): bool {
        $user = new User();
        $currentUser = $user->getCurrentUser();
        
        if (!$currentUser) {
            return false;
        }

        $post = $this->getById($id);
        if (!$post || ($post['user_id'] != $currentUser['id'] && $currentUser['role'] != 'admin')) {
            return false;
        }

        return (bool) $this->db->query("DELETE FROM posts WHERE id = ?", [$id]);
    }

    public function getPublished(): array {
        return $this->db->fetchAll(
            "SELECT p.*, u.name as author_name 
             FROM posts p 
             JOIN users u ON p.user_id = u.id 
             WHERE p.status = 'published' 
             ORDER BY p.created_at DESC"
        );
    }
}
