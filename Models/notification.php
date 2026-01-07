<?php
require_once __DIR__ . '/../core/Database.php';

class Notification {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getconnection();
        $this->ensureTable();
    }

    private function ensureTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS notifications (
                id INT PRIMARY KEY AUTO_INCREMENT,
                user_id INT NOT NULL,
                message TEXT NOT NULL,
                is_read TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
            )
        ";
        $this->db->exec($sql);
    }

    public function create($user_id, $message) {
        $sql = "INSERT INTO notifications (user_id, message) VALUES (:user_id, :message)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':user_id' => $user_id,
            ':message' => $message
        ]);
    }

    public function getByUser($user_id) {
        $sql = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead($id) {
        $sql = "UPDATE notifications SET seen = 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
