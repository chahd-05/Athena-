<?php
require_once __DIR__ . '/../core/Database.php';

class Comment {
    private $db;

    public function __construct() {
        $this->db = Database::getconnection();
    }
    public function create($task_id, $user_id, $content) {
        $stmt = $this->db->prepare("
            INSERT INTO comments (task_id, user_id, content)
            VALUES (:task_id, :user_id, :content)
        ");
        return $stmt->execute([
            ':task_id' => $task_id,
            ':user_id' => $user_id,
            ':content' => $content
        ]);
    }
    public function getByTask($task_id) {
        $stmt = $this->db->prepare("
            SELECT c.*, u.full_name
            FROM comments c
            JOIN user u ON c.user_id = u.id
            WHERE c.task_id = :task_id
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([':task_id' => $task_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
